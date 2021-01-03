<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortfolio extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shareholder' => 'required|numeric|gt:0', 
            'stock' => 'required|numeric|gt:0', 
            'offer' => 'required|numeric|gt:0', 
            'quantity' => 'required|numeric|gt:0', 
            'unit_cost' => 'required|numeric', 
            'effective_rate' => 'required|numeric', 
            'broker_commission' => 'nullable|numeric', 
            'sebon_commission' => 'nullable|numeric', 
            'dp_amount' => 'nullable|numeric', 
            'purchase_date' => 'nullable|date', 
            'total_amount' => 'required|numeric', 
            'base_amount' => 'required|numeric', 
            'broker_no' => 'nullable|numeric', 
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'stock.*' => 'Please select a :attribute',
            'shareholder.*' => 'Please select a :attribute name',
            'broker.*' => 'Please select a :attribute name',
            'offer.*' => 'Please select a :attribute',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'stock' => 'Symbol',
            'offer' => 'Offering type',
            'broker' => 'Broker',
            'shareholder' => 'Shareholder',
            'quantity' => 'Quantity',
            'unit_cost' => 'Unit cost',
            'effective_rate' => 'Effective rate',
            'purchase_date' => 'Purchase date',
            'receipt_number' => 'Receipt number',
            'total_amount' => 'Total amount',
        ];
    }

}
