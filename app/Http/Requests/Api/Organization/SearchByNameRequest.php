<?php

namespace App\Http\Requests\Api\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


/**
 * @OA\Schema(
 *     schema="SearchByNameRequest",
 *     required={"name"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Название компании",
 *         minLength=1,
 *         maxLength=255,
 *         description="Название организации или часть названия без учета регистра"
 *     )
 * )
 */
class SearchByNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];

    }

    public function messages(): array
    {
        return [
            'name.required' => 'Параметр name организации обязательно.',
            'name.string' => 'Название организации обязательно должно быть строкой.',
        ];
    }

}
