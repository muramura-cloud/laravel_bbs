<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        if (substr($this->path(), 0, 8) === 'comments') {
            return true;
        } else {
            return false;
        }
    }

    public function rules()
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'body' => 'required|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'body.required' => '本文は必須項目です。',
            'body.max' => '本文は2000文字以下です。',
        ];
    }
}
