<?php

namespace EscolaLms\CourseAccess\Http\Requests;

use EscolaLms\CourseAccess\Http\Requests\Abstracts\CourseAccessAPIRequest;
use Illuminate\Validation\Rule;

class AddAccessAPIRequest extends CourseAccessAPIRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'groups' => ['sometimes', 'array'],
            'groups.*' => ['integer', Rule::exists('groups', 'id')],
            'users' => ['sometimes', 'array'],
            'users.*' => ['integer', Rule::exists('users', 'id')],
        ]);
    }
}
