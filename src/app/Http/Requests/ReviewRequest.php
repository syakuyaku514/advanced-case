<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stars' => 'required',
            'comment' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'stars.required' => '評価は必ず選択してください',
            'comment.required' => 'コメントは必ず記入してください',
        ];
    }
}
