<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('financial_assistance_create');
    }

    public function rules()
    {
        return [
            'directory_id' => ['required', 'exists:directories,id'],

            // Type of assistance (inline list)
            'type_of_assistance' => [
                'nullable',
                'string',
                Rule::in([
                    'Financial Assistance',
                    'Guarantee Letter',
                    'Burial Assistance',
                    'Medical Assistance',
                    'Education Assistance',
                    'Solicitation',
                ]),
            ],



            'patient_name'        => ['nullable', 'string', 'max:255'],
            'claimant_is_patient' => ['nullable', 'boolean'],

            // Single checkbox group -> JSON/array
            'requirement_checklist'   => ['nullable', 'array'],
            'requirement_checklist.*' => ['string'],

            // Status (inline list; must match option values)
            'status' => ['nullable', Rule::in(['Claimed', 'Pending', 'Cancelled'])],

            // Problem presented (checkbox group -> array)
            'problem_presented_value'   => ['nullable', 'array'],
            'problem_presented_value.*' => ['string'],

            // Free-text SWO fields (since you’re not enforcing picks here)
            'social_welfare_name'  => ['nullable', 'string', 'max:255'],
            'social_welfare_desig' => ['nullable', 'string', 'max:255'],

            // Existing fields
            'problem_presented' => ['nullable', 'string'],
            'assessment'        => ['nullable', 'string'],
            'recommendation'    => ['nullable', 'string'],
            'amount'            => ['nullable', 'numeric'],
            'scheduled_fa'      => ['nullable', 'string'],
            // Use 'date' to accept Y-m-d or full datetime; switch to date_format if you require strictness
            'date_interviewed'  => ['nullable', 'date'],
            'date_claimed'      => ['nullable', 'date'],
            'note'              => ['nullable', 'string'],

            // Media (Spatie)
            'requirements'   => ['sometimes', 'array'],
            'requirements.*' => ['string'],
        ];

        $validated = $request->validate([
            // ...
            'reference_no' => ['nullable', 'string', 'max:50', 'unique:financial_assistances,reference_no'],
        ]);
    }
}
