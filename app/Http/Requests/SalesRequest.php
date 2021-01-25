<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
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
            'stock_id' => 'required|numeric|gt:0', 
            'shareholder_id' => 'required|numeric|gt:0', 
            'quantity' => 'required|numeric|gt:0', 
            'wacc' => 'required|numeric', 
            'cost_price' => 'nullable|numeric', 
            'sell_price' => 'required|numeric', 
            'net_receivable' => 'nullable|numeric', 
            'sales_date' => 'nullable|date', 
            'payment_date' => 'nullable|date', 
            'broker_commission' => 'nullable|numeric', 
            'sebon_commission' => 'nullable|numeric', 
            'capital_gain_tax' => 'nullable|numeric', 
            'gain' => 'nullable|numeric', 
            'dp_amount' => 'nullable|numeric', 
            'name_transfer' => 'nullable|numeric', 
            'broker_no' => 'nullable|numeric', 
        ];
    }
}
