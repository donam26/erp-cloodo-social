<?php

namespace App\Http\Requests\PostRequest;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string',
            'privacy' => 'required|string|in:public,private,friends',
            'images' => 'nullable|array',
            'images.*' => ['nullable', 'string', 'regex:/^data:image\/(jpeg|png|jpg|gif);base64,/']
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Nội dung không được để trống',
            'privacy.required' => 'Quyền riêng tư không được để trống',
            'privacy.in' => 'Quyền riêng tư không hợp lệ',
            'images.array' => 'Dữ liệu ảnh không hợp lệ',
            'images.*.regex' => 'Định dạng ảnh base64 không hợp lệ'
        ];
    }
}

