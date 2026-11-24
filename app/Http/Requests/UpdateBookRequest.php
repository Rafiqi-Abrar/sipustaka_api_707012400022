<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
   public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'judul' => ['sometimes', 'required', 'string', 'max:200'],
        'penulis' => ['sometimes', 'required', 'string', 'max:100'],
        'penerbit' => ['sometimes', 'required', 'string', 'max:100'],
        'tahun_terbit' => ['sometimes', 'required', 'integer', 'min:1000', 'max:2100'],
        'stok' => ['sometimes', 'required', 'integer', 'min:0'],
        'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ];
}
}
