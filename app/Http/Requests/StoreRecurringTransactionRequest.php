<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission('create-rtransaction');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                       => 'required|string',
            'description'                => 'nullable|string',
            'amount'                     => 'required|numeric|min:0',
            'transaction_type'           => [new Enum(TransactionType::class)],
            'active'                     => 'required|boolean',
            'budget_id'                  => 'required|exists:budgets,id',
            'category_id'                => 'required|exists:categories,id',
            'payee_id'                   => 'required|exists:payees,id',
            'time_period_id'             => 'required|exists:time_periods,id',
            'time_period_amount'         => 'nullable|numeric',
            'starts_at'                  => 'required|date|after_or_equal:today',
            'ends_at'                    => 'nullable|date|after:starts_at',
        ];
    }
}
