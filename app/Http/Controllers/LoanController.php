<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;

class LoanController extends Controller
{
    // Melihat peminjaman milik pengguna
    public function index(Request $request)
    {
        $loans = $request->user()
            ->loans()
            ->with('book')
            ->latest()
            ->get();

        return response()->json($loans);
    }

    // Menambahkan peminjaman
    public function store(StoreLoanRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {

            $book = Book::where('id', $data['book_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($book->stok < 1) {
                return response()->json([
                    'message' => 'Stok buku habis'
                ], 422);
            }

            $loan = $request->user()->loans()->create([
                'book_id' => $data['book_id'],
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'tanggal_kembali' => $data['tanggal_kembali'] ?? null,
                'status' => 'Dipinjam',
            ]);

            $book->decrement('stok');

            return response()->json([
                'message' => 'Peminjaman berhasil',
                'data' => $loan->load('book')
            ], 201);
        });
    }

    // Melihat detail peminjaman
    public function show(Request $request, $id)
    {
        $loan = $this->findOwnedLoan($request, $id);

        return response()->json($loan->load('book'));
    }

    // Mengubah status peminjaman
    public function update(UpdateLoanRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $loan = $this->findOwnedLoan($request, $id);

            $data = $request->validated();

            if (
                $loan->status !== 'Dikembalikan' &&
                $data['status'] === 'Dikembalikan'
            ) {
                Book::where('id', $loan->book_id)
                    ->increment('stok');

                $data['tanggal_kembali'] =
                    $data['tanggal_kembali'] ?? now()->toDateString();
            }

            if (
                $loan->status === 'Dikembalikan' &&
                $data['status'] === 'Dipinjam'
            ) {
                $book = Book::where('id', $loan->book_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($book->stok < 1) {
                    return response()->json([
                        'message' => 'Stok buku habis'
                    ], 422);
                }

                $book->decrement('stok');
                $data['tanggal_kembali'] = null;
            }

            $loan->update($data);

            return response()->json([
                'message' => 'Peminjaman berhasil diperbarui',
                'data' => $loan
            ]);
        });
    }

    // Menghapus peminjaman
    public function destroy(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $loan = $this->findOwnedLoan($request, $id);

            if ($loan->status === 'Dipinjam') {
                Book::where('id', $loan->book_id)
                    ->increment('stok');
            }

            $loan->delete();

            return response()->json([
                'message' => 'Peminjaman berhasil dihapus'
            ]);
        });
    }

    // Memeriksa kepemilikan peminjaman
    private function findOwnedLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->user_id != $request->user()->id) {
            abort(403, 'Anda tidak memiliki izin');
        }

        return $loan;
    }
}