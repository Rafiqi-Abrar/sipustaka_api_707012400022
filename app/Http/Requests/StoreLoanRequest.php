<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
   public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'book_id' => ['required', 'exists:books,id'],
        'tanggal_pinjam' => ['required', 'date'],
        'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
    ];
}
}
