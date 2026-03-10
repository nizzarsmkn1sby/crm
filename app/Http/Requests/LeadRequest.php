<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:50',
            'whatsapp'            => 'nullable|string|max:50',
            'company'             => 'nullable|string|max:255',
            'position'            => 'nullable|string|max:255',
            'source'              => 'required|string',
            'status'              => 'required|string',
            'priority'            => 'required|string',
            'estimated_value'     => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'tags'                => 'nullable|string',
            'assigned_to'         => 'nullable|exists:users,id',
            'pipeline_stage_id'   => 'nullable|exists:pipeline_stages,id',
            'expected_close_date' => 'nullable|date',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                => 'Nama Lead',
            'email'               => 'Email',
            'phone'               => 'Telepon',
            'source'              => 'Sumber',
            'status'              => 'Status',
            'priority'            => 'Prioritas',
            'estimated_value'     => 'Nilai Estimasi',
            'assigned_to'         => 'Salesperson',
            'pipeline_stage_id'   => 'Stage Pipeline',
            'expected_close_date' => 'Tanggal Target',
        ];
    }
}
