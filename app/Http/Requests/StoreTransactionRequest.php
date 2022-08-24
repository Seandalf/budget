<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission('create-transaction');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                     => 'required|string|max:50',
            'description'              => 'nullable|string|max:200',
            'budget'                   => 'nullable|numeric|min:0',
            'actual'                   => 'nullable|numeric|min:0',
            'record_number'            => 'nullable|string',
            'type'                     => [new Enum(TransactionType::class)],
            'interval_id'              => 'required|numeric|exists:intervals,id',
            'category_id'              => 'required|numeric|exists:categories,id',
            'payee_id'                 => 'nullable|numeric|exists:payees,id',
            'recurring_transaction_id' => 'nullable|numeric|exists:recurring_transactions,id',
            'group_transaction_id'     => 'nullable|numeric|exists:group_transactions,id',
            'paid_at'                  => 'nullable|date|before_or_equal:today',
        ];
    }
}
