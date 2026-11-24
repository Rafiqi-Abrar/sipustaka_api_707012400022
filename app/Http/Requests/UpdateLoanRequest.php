<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'tanggal_kembali' => ['nullable', 'date'],
        'status' => ['required', 'in:Dipinjam,Dikembalikan'],
    ];
}
}
