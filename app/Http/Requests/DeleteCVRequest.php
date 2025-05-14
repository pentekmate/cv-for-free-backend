<?php

namespace App\Http\Requests;

use App\Models\CV;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteCVRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $cvId = $this->input('cvId');

        if (! $cvId) {
            return true;
        }

        $cv = CV::find($cvId);

        if (! $cv) {
            return true;
        }

        return $cv && $cv->user_id === auth()->id(); // vagy Auth::id()
    }

    public function forbiddenResponse()
    {
        return response()->json(['error' => 'Nincs jogosultságod ehhez a művelethez.'], 403);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cvId' => 'required|exists:cvs,id',
        ];

    }

    public function messages()
    {
        return [
            'cvId.required' => 'A cv-idja közeleő.',
            'cvId.exists' => 'Nem található cv.',
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
