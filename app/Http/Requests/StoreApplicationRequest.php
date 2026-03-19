<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'applicant';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_of_assessment_applied_for' => ['required','string','max:255'],
            'application_type' => ['nullable','in:TWSP,Assessment Only'],
            'photo' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'surname' => ['required','string','max:255'],
            'firstname' => ['required','string','max:255'],
            'middlename' => ['nullable','string','max:255'],
            'middleinitial' => ['nullable','string','max:5'],
            'name_extension' => ['nullable','string','max:32'],

            'region_code' => ['required','string'],
            'region_name' => ['required','string'],
            'province_code' => ['required','string'],
            'province_name' => ['required','string'],
            'city_code' => ['required','string'],
            'city_name' => ['required','string'],
            'barangay_code' => ['required','string'],
            'barangay_name' => ['required','string'],
            'district' => ['nullable','string','max:255'],
            'street_address' => ['nullable','string','max:255'],
            'zip_code' => ['nullable','string','max:10'],

            'mothers_name' => ['nullable','string','max:255'],
            'fathers_name' => ['nullable','string','max:255'],
            'sex' => ['nullable','in:male,female,prefer_not_to_say'],
            'civil_status' => ['nullable','string','max:100'],
            'mobile' => ['nullable','string','max:32'],
            'email' => ['nullable','email'],

            'highest_educational_attainment' => ['nullable','string','max:255'],
            'employment_status' => ['nullable','string','max:255'],

            'birthdate' => ['nullable','date'],
            'birthplace' => ['nullable','string','max:255'],
            'age' => ['nullable','integer','min:0','max:120'],
            // NEW FIELDS FOR PAGES 3-4
            'nationality' => ['nullable','string','max:255'],
            'employment_before_training_status' => ['nullable','string','in:wage-employed,underemployed,self-employed,unemployed'],
            'employment_before_training_type' => ['nullable','string','in:regular,casual,job order,probationary,permanent,contractual,temporary'],
            'birthplace_city' => ['nullable','string','max:255'],
            'birthplace_province' => ['nullable','string','max:255'],
            'birthplace_region' => ['nullable','string','max:255'],
            'educational_attainment_before_training' => ['nullable','string','max:255'],
            'parent_guardian_name' => ['nullable','string','max:255'],
            'parent_guardian_street' => ['nullable','string','max:255'],
            'parent_guardian_barangay' => ['nullable','string','max:255'],
            'parent_guardian_district' => ['nullable','string','max:255'],
            'parent_guardian_city' => ['nullable','string','max:255'],
            'parent_guardian_province' => ['nullable','string','max:255'],
            'parent_guardian_region' => ['nullable','string','max:255'],
            'learner_classification' => ['nullable','array'],
            'learner_classification.*' => ['string','max:255'],
            'scholarship_type' => ['nullable','string','max:255'],
            'privacy_consent' => ['required','accepted'],

            // Arrays
            'work_experiences' => ['array'],
            'work_experiences.*.company_name' => ['required_with:work_experiences','string','max:255'],
            'work_experiences.*.position' => ['nullable','string','max:255'],
            'work_experiences.*.date_from' => ['nullable','date'],
            'work_experiences.*.date_to' => ['nullable','date','after_or_equal:work_experiences.*.date_from'],
            'work_experiences.*.monthly_salary' => ['nullable','numeric'],
            'work_experiences.*.appointment_status' => ['nullable','string','max:255'],
            'work_experiences.*.years_experience' => ['nullable','integer','min:0'],

            'trainings' => ['array'],
            'trainings.*.title' => ['required_with:trainings','string','max:255'],
            'trainings.*.venue' => ['nullable','string','max:255'],
            'trainings.*.date_from' => ['nullable','date'],
            'trainings.*.date_to' => ['nullable','date','after_or_equal:trainings.*.date_from'],
            'trainings.*.hours' => ['nullable','integer','min:0'],
            'trainings.*.conducted_by' => ['nullable','string','max:255'],

            'licensure_exams' => ['array'],
            'licensure_exams.*.title' => ['required_with:licensure_exams','string','max:255'],
            'licensure_exams.*.year_taken' => ['nullable','digits:4'],
            'licensure_exams.*.exam_venue' => ['nullable','string','max:255'],
            'licensure_exams.*.rating' => ['nullable','string','max:255'],
            'licensure_exams.*.remarks' => ['nullable','string','max:255'],
            'licensure_exams.*.expiry_date' => ['nullable','date'],

            'competency_assessments' => ['array'],
            'competency_assessments.*.title' => ['required_with:competency_assessments','string','max:255'],
            'competency_assessments.*.qualification_level' => ['nullable','string','max:255'],
            'competency_assessments.*.industry_sector' => ['nullable','string','max:255'],
            'competency_assessments.*.certificate_number' => ['nullable','string','max:255'],
            'competency_assessments.*.date_of_issuance' => ['nullable','date'],
            'competency_assessments.*.expiration_date' => ['nullable','date'],
        ];

        if ($this->input('application_type') === 'TWSP') {
            $rules = array_merge($rules, [
                'psa_birth_certificate' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
                'psa_marriage_contract' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
                'high_school_document' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
                'id_pictures_1x1' => ['required','array','min:1','max:4'],
                'id_pictures_1x1.*' => ['required','file','mimes:jpg,jpeg,png','max:2048'],
                'id_pictures_passport' => ['required','array','min:1','max:4'],
                'id_pictures_passport.*' => ['required','file','mimes:jpg,jpeg,png','max:2048'],
                'government_school_id' => ['required','array','min:1','max:2'],
                'government_school_id.*' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
                'certificate_of_indigency' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            ]);
        }
                return $rules;
    }
    public function messages(): array
    {
        return [
            'application_type.in' => 'Invalid application type selected.',
            'psa_birth_certificate.required' => 'PSA Birth Certificate is required for TWSP.',
            'psa_birth_certificate.max' => 'PSA Birth Certificate must not exceed 5MB.',
            'high_school_document.required' => 'High School document is required for TWSP.',
            'id_pictures_1x1.required' => 'Please upload 1x1 ID pictures.',
            'id_pictures_1x1.size' => 'Please upload exactly of 1x1 ID pictures.',
            'id_pictures_passport.required' => 'Please upload of passport size pictures.',
            'id_pictures_passport.size' => 'Please upload exactly  of passport size pictures.',
            'government_school_id.required' => 'Please upload  of Government/School ID.',
            'government_school_id.size' => 'Please upload exactly of Government/School ID.',
            'certificate_of_indigency.required' => 'Certificate of Indigency is required for TWSP.',
        ];
    }
}
