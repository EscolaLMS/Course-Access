<?php

namespace EscolaLms\CourseAccess\Http\Requests;

use EscolaLms\CourseAccess\Http\Requests\Abstracts\CourseAccessAPIRequest;
use Illuminate\Validation\Rule;

class SetAccessAPIRequest extends CourseAccessAPIRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'groups' => ['sometimes', 'array'],
            'groups.*' => ['sometimes', 'integer', Rule::exists('groups', 'id')],
            'users' => ['sometimes', 'array'],
            'users.*' => ['sometimes', 'integer', Rule::exists('users', 'id')],
        ]);
    }
}
