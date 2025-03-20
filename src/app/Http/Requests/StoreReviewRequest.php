<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
        'review_content' => 'required|string|max:400',
        'stars' => 'required|integer|min:1|max:5',
        'review_image' => 'required|array',
        'review_image.*' => 'image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
        'review_content.required' => '口コミを入力してください。',
        'review_content.max' => '口コミは400文字以内で入力してください。',
        'stars.required' => '評価（星）を選択してください。',
        'stars.integer' => '評価（星）は数値で入力してください。',
        'stars.min' => '評価（星）は1以上にしてください。',
        'stars.max' => '評価（星）は5以下にしてください。',
        'review_image.required' => '画像を追加してください。',
        'review_image.array' => '画像をアップロードしてください。',
        'review_image.*.image' => 'アップロードできるのは画像ファイルのみです。',
        'review_image.*.mimes' => 'アップロードできる画像はJPEGまたはPNGのみです。',
        'review_image.*.max' => '画像のサイズは2MB以下にしてください。',
        ];
    }
}
