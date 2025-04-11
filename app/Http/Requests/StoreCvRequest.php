<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.user_id' => 'integer|required',
            'data.cv_type_id' => 'integer|required',
            'data.userName' => 'string|nullable', // no needed
            'data.background' => 'string|nullable',
            'data.image' => 'string|nullable',
            'data.firstName' => 'string|nullable',
            'data.lastName' => 'string|nullable',
            'data.phoneNumber' => 'string|nullable',
            'data.email' => 'email|nullable',
            'data.country' => 'string|nullable',
            'data.city' => 'string|nullable',
            'data.jobTitle' => 'string|nullable',
            'data.introduce' => 'string|nullable',
            'data.age' => 'integer|nullable|min:18|max:100',
            'data.ethnic' => 'string|nullable',
            'data.blob' => 'file|mimes:pdf|max:10240',

            'previousJobs' => 'array|nullable',
            'previousJobs.*.employer' => 'string|nullable',
            'previousJobs.*.jobTitle' => 'string|nullable',
            'previousJobs.*.startDate' => 'date|nullable',
            'previousJobs.*.endDate' => 'date|nullable|after_or_equal:previousJobs.*.startDate',
            'previousJobs.*.description' => 'string|nullable',
            'previousJobs.*.city' => 'string|nullable',

            'skills' => 'array|nullable',
            'skills.*.skillName' => 'string|nullable',
            'skills.*.skillLevel' => 'integer|nullable',

            'languages' => 'array|nullable',
            'languages.*.languageName' => 'string|nullable',
            'languages.*.languageLevel' => 'integer|nullable',

            'schools' => 'array|nullable',
            'school.*.schoolName' => 'string|nullable',
            'school.*.degree' => 'string|nullable',
            'school.*.startDate' => 'date|nullable',
            'school.*.endDate' => 'date|nullable|after_or_equal:school.*.startDate',
            'school.*.city' => 'string|nullable',

            'socials' => 'array|nullable',
            'socials.*.name' => 'string|nullable',
            'socials.*.link' => 'string|nullable',

            'licenses' => 'array|nullable',
            'licenses.*.type' => 'string|nullable',
        ];

    }

    public function messages()
    {
        return [
            'data.user_id.required' => 'A felhasználó azonosítója kötelező!',
            'data.user_id.integer' => 'A felhasználó azonosítójának egész számnak kell lennie!',
            'data.background.string' => 'Nincs kiválasztott szín!',
            'data.userName.string' => 'A felhasználónévnek szövegnek kell lennie!',
            'data.image.string' => 'A képnek szövegnek kell lennie!',
            'data.firstName.string' => 'A keresztnevet szövegként kell megadni!',
            'data.lastName.string' => 'A vezetéknevet szövegként kell megadni!',
            'data.phoneNumber.string' => 'A telefonszámnak szövegnek kell lennie!',
            'data.email.email' => 'Érvényes email címnek kell lennie!',
            'data.country.string' => 'Az ország neve szövegként kell megadni!',
            'data.city.string' => 'A város neve szövegként kell megadni!',
            'data.jobTitle.string' => 'A munkakör címének stringnek kell lennie!',
            'data.introduce.string' => 'Az önbemutatónak szövegnek kell lennie!',
            'data.age.integer' => 'A kornak egész számnak kell lennie!',
            'data.age.min' => 'A kor legalább 18 éves kell legyen!',
            'data.age.max' => 'A kor nem haladhatja meg a 100 évet!',
            'data.ethnic.string' => 'A nemzetiségi adatnak szövegnek kell lennie!',
            'data.blob' => 'Hiányzó fájl!',
            'previousJobs.array' => 'A korábbi munkák listája tömb formájában kell legyen!',
            'previousJobs.*.employer.string' => 'A munkaadó neve szövegnek kell lennie!',
            'previousJobs.*.jobTitle.string' => 'A munkakör címének szövegnek kell lennie!',
            'previousJobs.*.startDate.date' => 'A kezdő dátumnak dátum formátumban kell lennie!',
            'previousJobs.*.endDate.date' => 'A végdátumnak dátum formátumban kell lennie!',
            'previousJobs.*.endDate.after_or_equal' => 'A végdátumnak nem lehet korábbi, mint a kezdődátum!',
            'previousJobs.*.description.string' => 'A munkakör leírásának szövegnek kell lennie!',
            'previousJobs.*.city.string' => 'A városnak szövegnek kell lennie!',
            'skills.array' => 'A készségek listája tömb formájában kell legyen!',
            'skills.*.skillName.string' => 'A készség nevének szövegnek kell lennie!',
            'skills.*.skillLevel.integer' => 'A készségi szintnek egész számnak kell lennie!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Hiba történt a validálás során!',
            'errors' => $validator->errors(),
        ], 422));
    }
}
