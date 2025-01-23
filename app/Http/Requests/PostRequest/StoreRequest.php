<?php

namespace App\Http\Requests\PostRequest;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Nội dung không được để trống',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Định dạng ảnh không hợp lệ',
            'images.*.max' => 'Kích thước ảnh không được quá 2MB'
        ];
    }
}

