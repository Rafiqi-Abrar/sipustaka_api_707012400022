<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    // Menampilkan semua buku
    public function index()
    {
        return response()->json(Book::latest()->get());
    }

    // Menambahkan buku
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')
                ->store('books', 'local');
        }

        $book = Book::create($data);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    // Menampilkan satu buku
    public function show($id)
    {
        $book = Book::findOrFail($id);

        return response()->json($book);
    }

    // Mengubah buku
    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('cover')) {

            if ($book->cover) {
                Storage::disk('local')->delete($book->cover);
            }

            $data['cover'] = $request->file('cover')
                ->store('books', 'local');
        }

        $book->update($data);

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $book
        ]);
    }

    // Menghapus buku
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover) {
            Storage::disk('local')->delete($book->cover);
        }

        $book->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ]);
    }

    // Menampilkan cover buku
    public function cover($id)
    {
        $book = Book::findOrFail($id);

        if (!$book->cover ||
            !Storage::disk('local')->exists($book->cover)) {
            abort(404, 'Cover buku tidak ditemukan');
        }

        return response()->file(
            Storage::disk('local')->path($book->cover)
        );
    }
}