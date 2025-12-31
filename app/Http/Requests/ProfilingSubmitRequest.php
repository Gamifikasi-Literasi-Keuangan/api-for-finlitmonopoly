<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilingSubmitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'answers'   => 'required|array|min:3',

            'answers.*.question_code' => [
                'required',
                'string',
                'exists:profiling_questions,question_code',
            ],

            'answers.*.option_token' => [
                'required',
                'string',
                'exists:profiling_question_options,option_token',
            ],

            'profiling_done' => 'boolean|nullable'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
