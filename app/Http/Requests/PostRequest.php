<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (substr($this->path(), 0, 5) === 'posts') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:2000',
            'img' => 'file|mimes:jpeg,png,jpg,gif|max:1024',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'タイトルは必須項目です。',
            'title.max' => 'タイトルは50文字以下です。',
            'body.required' => '本文は必須項目です。',
            'body.max' => '本文は2000文字以下です。',
            'img.file' => '画像はフォームからアップロードされる必要があります。',
            'img.mimes' => '画像の形式はjpeg,png,jpg,gifのいずれかです。',
            'img.max' => '画像のサイズは1MB以下です。',
        ];
    }
}
