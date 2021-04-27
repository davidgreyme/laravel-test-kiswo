<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string', 'max:255', Rule::unique((new \App\Models\User)->getTable())->ignore($this->route()->user->id ?? null)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique((new \App\Models\User)->getTable())->ignore($this->route()->user->id ?? null)],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

}
