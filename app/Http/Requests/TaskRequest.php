<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100|',
            'description' => 'required|string|max:1000|',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:new,in_progress,completed',
            'start_date' => 'required|date|date',
            'end_date' => 'required|date|after_or_equal:start_date',
//            'auth_id' => 'required|exists:auths,id',
        ];
    }
}
