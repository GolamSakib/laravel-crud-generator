<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required'],
            'order_number' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,processing,completed'],
            'total' => ['required'],
            'notes' => ['required']
        ];
    }
}