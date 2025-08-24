<?php

namespace App\Http\Requests\Api\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SearchByGeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Параметры для поиска по радиусу
            'lat' => [
                'required_with:lng,radius',
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'lng' => [
                'required_with:lat,radius',
                'nullable',
                'numeric',
                'between:-180,180',
            ],
            'radius' => [
                'required_with:lat,lng',
                'nullable',
                'numeric',
                'min:0',
            ],

            // Параметры для поиска по прямоугольной области
            'min_lat' => [
                'required_with:min_lng,max_lat,max_lng',
                'nullable',
                'numeric',
                'between:-90,90',
                'lte:max_lat',
            ],
            'min_lng' => [
                'required_with:min_lat,max_lat,max_lng',
                'nullable',
                'numeric',
                'between:-180,180',
                'lte:max_lng',
            ],
            'max_lat' => [
                'required_with:min_lat,min_lng,max_lng',
                'nullable',
                'numeric',
                'between:-90,90',
                'gte:min_lat',
            ],
            'max_lng' => [
                'required_with:min_lat,min_lng,max_lat',
                'nullable',
                'numeric',
                'between:-180,180',
                'gte:min_lng',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'lat.required_with' => 'Широта обязательна при указании долготы и радиуса',
            'lng.required_with' => 'Долгота обязательна при указании широты и радиуса',
            'radius.required_with' => 'Радиус обязателен при указании координат',
            'radius.min' => 'Радиус должен быть положительным числом',

            'min_lat.required_with' => 'Минимальная широта обязательна при указании границ области',
            'min_lng.required_with' => 'Минимальная долгота обязательна при указании границ области',
            'max_lat.required_with' => 'Максимальная широта обязательна при указании границ области',
            'max_lng.required_with' => 'Максимальная долгота обязательна при указании границ области',

            'min_lat.lte' => 'Минимальная широта должна быть меньше или равна максимальной',
            'min_lng.lte' => 'Минимальная долгота должна быть меньше или равна максимальной',
            'max_lat.gte' => 'Максимальная широта должна быть больше или равна минимальной',
            'max_lng.gte' => 'Максимальная долгота должна быть больше или равна минимальной',

            '*.numeric' => 'Поле должно быть числом',
            'lat.between' => 'Широта должна быть между -90 и 90',
            'lng.between' => 'Долгота должна быть между -180 и 180',
            'min_lat.between' => 'Минимальная широта должна быть между -90 и 90',
            'min_lng.between' => 'Минимальная долгота должна быть между -180 и 180',
            'max_lat.between' => 'Максимальная широта должна быть между -90 и 90',
            'max_lng.between' => 'Максимальная долгота должна быть между -180 и 180',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            $data = $this->all();


            $hasRadiusParams = isset($data['lat']) && $data['lat'] !== null &&
                isset($data['lng']) && $data['lng'] !== null &&
                isset($data['radius']) && $data['radius'] !== null;


            $hasBboxParams = isset($data['min_lat']) && $data['min_lat'] !== null &&
                isset($data['min_lng']) && $data['min_lng'] !== null &&
                isset($data['max_lat']) && $data['max_lat'] !== null &&
                isset($data['max_lng']) && $data['max_lng'] !== null;


            if (!$hasRadiusParams && !$hasBboxParams) {
                $hasAnyParam = isset($data['lat']) || isset($data['lng']) || isset($data['radius']) ||
                    isset($data['min_lat']) || isset($data['min_lng']) ||
                    isset($data['max_lat']) || isset($data['max_lng']);

                if ($hasAnyParam) {
                    $validator->errors()->add(
                        'parameters',
                        'Необходимо указать либо все параметры для поиска по радиусу (lat, lng, radius), ' .
                        'либо все параметры для поиска по прямоугольной области (min_lat, min_lng, max_lat, max_lng)'
                    );
                } else {
                    $validator->errors()->add(
                        'parameters',
                        'Необходимо указать параметры для поиска: либо по радиусу (lat, lng, radius), ' .
                        'либо по прямоугольной области (min_lat, min_lng, max_lat, max_lng)'
                    );
                }
            }

            if ($hasRadiusParams && $hasBboxParams) {
                $validator->errors()->add(
                    'parameters',
                    'Нельзя одновременно использовать поиск по радиусу и по прямоугольной области. ' .
                    'Выберите один из вариантов.'
                );
            }
        });
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'lat' => $this->toFloat('lat'),
            'lng' => $this->toFloat('lng'),
            'radius' => $this->toFloat('radius'),
            'min_lat' => $this->toFloat('min_lat'),
            'min_lng' => $this->toFloat('min_lng'),
            'max_lat' => $this->toFloat('max_lat'),
            'max_lng' => $this->toFloat('max_lng'),
        ]);
    }

    private function toFloat($key)
    {
        $value = $this->input($key);

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }
}
