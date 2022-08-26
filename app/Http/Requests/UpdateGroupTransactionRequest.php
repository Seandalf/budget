<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateGroupTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission('update-gtransaction');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'budget'      => 'nullable|numeric',
            'actual'      => 'nullable|numeric',
            'type'        => [new Enum(TransactionType::class)],
            'final'       => 'required|boolean',
            'interval_id' => 'required|exists:intervals,id',
            'category_id' => 'required|exists:categories,id',
            'payee_id'    => 'nullable|exists:payees,id',
        ];
    }
}
