<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'judul' => ['required', 'string', 'max:200'],
        'penulis' => ['required', 'string', 'max:100'],
        'penerbit' => ['required', 'string', 'max:100'],
        'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:2100'],
        'stok' => ['required', 'integer', 'min:0'],
        'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ];
}
}
