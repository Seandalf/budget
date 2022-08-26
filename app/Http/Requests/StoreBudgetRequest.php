<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission('create-budget');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'               => 'required|string',
            'description'        => 'nullable|string',
            'opening_balance'    => 'required|numeric',
            'closing_balance'    => 'required|numeric',
            'future_intervals'   => 'required|numeric|min:6|max:24',
            'currency_id'        => 'required|exists:currencies,id',
            'time_period_id'     => 'required|exists:time_periods,id',
            'time_period_amount' => 'nullable|numeric',
            'starts_at'          => 'required|date|after_or_equal:today',
        ];
    }
}
