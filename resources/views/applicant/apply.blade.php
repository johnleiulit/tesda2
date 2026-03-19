<!-- resources/views/applicant/apply.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Application Form</h3>
                <!-- Progress Bar -->
                <div class="progress mt-3" style="height: 30px;" id="progress-bar-container">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progress-bar"
                        role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                        Step 1 of 5
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Application Type Banner -->
                <div id="application-info-banner" class="alert alert-info mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span id="banner-message"></span>
                        <button type="button" class="btn-close" id="close-banner" aria-label="Close"></button>
                    </div>
                </div>

                <form method="POST" action="{{ route('applicant.apply.store') }}" enctype="multipart/form-data"
                    id="application-form">
                    @csrf

                    <!-- STEP 0: TWSP Documents (Hidden by default, shown only for TWSP) -->
                    <div id="step-0" class="form-step" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>TWSP Requirements:</strong> Please upload all required documents before proceeding.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{-- PSA Birth Certificate --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        PSA Copy of Birth Certificate
                                    </h5>
                                    <input type="file" class="form-control" name="psa_birth_certificate"
                                        id="psa_birth_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                                    <div class="mt-2" id="preview_psa_birth_certificate"></div>
                                </div>

                                {{-- PSA Marriage Contract --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-secondary me-2">Optional</span>
                                        PSA Copy of Marriage Contract (if applicable)
                                    </h5>
                                    <input type="file" class="form-control" name="psa_marriage_contract"
                                        id="psa_marriage_contract" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                                    <div class="mt-2" id="preview_psa_marriage_contract"></div>
                                </div>

                                {{-- High School Document --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        High School Card, TOR, or Diploma
                                    </h5>
                                    <input type="file" class="form-control" name="high_school_document"
                                        id="high_school_document" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                                    <div class="mt-2" id="preview_high_school_document"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                {{-- 1x1 ID Pictures --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        4 pcs. 1x1 ID Picture
                                    </h5>
                                    <input type="file" class="form-control" name="id_pictures_1x1[]" id="id_pictures_1x1"
                                        accept=".jpg,.jpeg,.png" multiple>
                                    <small class="text-muted">(Upload 1-4 files)</small>
                                    <div class="mt-2" id="preview_id_pictures_1x1"></div>
                                </div>

                                {{-- Passport Size Pictures --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        4 pcs. Passport Size (White background, collar, full name)
                                    </h5>
                                    <input type="file" class="form-control" name="id_pictures_passport[]"
                                        id="id_pictures_passport" accept=".jpg,.jpeg,.png" multiple>
                                    <small class="text-muted">(Upload 1-4 files)</small>
                                    <div class="mt-2" id="preview_id_pictures_passport"></div>
                                </div>

                                {{-- Government/School ID --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        2 pcs. Government/School ID (Photocopy)
                                    </h5>
                                    <input type="file" class="form-control" name="government_school_id[]"
                                        id="government_school_id" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    <small class="text-muted">(Upload 1-2 files)</small>
                                    <div class="mt-2" id="preview_government_school_id"></div>
                                </div>

                                {{-- Certificate of Indigency --}}
                                <div class="mb-4">
                                    <h5 class="text-primary fw-bold">
                                        <span class="badge bg-danger me-2">Required</span>
                                        Certificate of Indigency from Barangay
                                    </h5>
                                    <input type="file" class="form-control" name="certificate_of_indigency"
                                        id="certificate_of_indigency" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                                    <div class="mt-2" id="preview_certificate_of_indigency"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary px-4" id="cancel-step-0">
                                <i class="fas fa-times"></i> Cancel Application
                            </button>
                            <button type="button" class="btn btn-primary px-4" id="next-from-step-0">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 1: Program Selection & Photo -->
                    <div id="step-1" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 1: Program Selection & Photo</h4>

                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="text-primary fw-bold form-label" for="title_of_assessment_applied_for">
                                        Title of Assessment Applied For <span class="text-danger">*</span>
                                    </label>
                                    <select name="title_of_assessment_applied_for" class="form-select"
                                        id="title_of_assessment_applied_for" required>
                                        <option value="">Select Title of Assessment</option>
                                        <option value="BOOKKEEPING NC III">BOOKKEEPING NC III</option>
                                        <option value="EVENTS MANAGEMENT SERVICES NC III">EVENTS MANAGEMENT SERVICES NC III
                                        </option>
                                        <option value="TOURISM PROMOTION SERVICES NC II">TOURISM PROMOTION SERVICES NC II
                                        </option>
                                        <option value="PHARMACY SERVICES NC III">PHARMACY SERVICES NC III</option>
                                        <option value="VISUAL GRAPHIC DESIGN NC III">VISUAL GRAPHIC DESIGN NC III</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mt-2">
                                    <label class="text-primary fw-bold" for="photo">
                                        Photo (Colored, Passport Size) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" name="photo" id="photo"
                                        accept="image/jpeg,image/png,image/jpg" required>

                                    <div class="text-center">
                                        <img id="photo-preview" src="" alt="Preview" class="img-thumbnail"
                                            style="max-width: 150px; max-height: 150px; display: none; border: 3px solid #000000;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-secondary px-4" id="cancel-step-1">
                                    <i class="fas fa-times"></i> Cancel Application
                                </button>
                                <button type="button" class="btn btn-outline-secondary px-4 ms-2" id="back-to-step-0"
                                    style="display: none;">
                                    <i class="fas fa-arrow-left"></i> Back
                                </button>
                            </div>
                            <button type="button" class="btn btn-primary px-4" id="next-to-step-2">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Personal Information (Identity) -->
                    <div id="step-2" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 2: Personal Information</h4>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Full Name</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Surname <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="surname" placeholder="Surname"
                                            value="{{ old('surname') }}" style="text-transform: uppercase;" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="firstname"
                                            placeholder="First name" value="{{ old('firstname') }}"
                                            style="text-transform: uppercase;" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" style="text-transform: uppercase;"
                                            name="middlename" placeholder="Middle name" value="{{ old('middlename') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Middle Initial</label>
                                        <input type="text" class="form-control" style="text-transform: uppercase;"
                                            name="middleinitial" placeholder="MI" value="{{ old('middleinitial') }}"
                                            maxlength="2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Name Extension</label>
                                        <select name="name_extension" class="form-select">
                                            <option value="">None</option>
                                            <option value="Jr." {{ old('name_extension') == 'Jr.' ? 'selected' : '' }}>
                                                Jr.</option>
                                            <option value="Sr." {{ old('name_extension') == 'Sr.' ? 'selected' : '' }}>
                                                Sr.</option>
                                            <option value="II" {{ old('name_extension') == 'II' ? 'selected' : '' }}>II
                                            </option>
                                            <option value="III" {{ old('name_extension') == 'III' ? 'selected' : '' }}>
                                                III</option>
                                            <option value="IV" {{ old('name_extension') == 'IV' ? 'selected' : '' }}>
                                                IV</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Birth Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="birthdate"
                                            value="{{ old('birthdate') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Birthplace <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace"
                                            placeholder="City/Municipality" value="{{ old('birthplace') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Age <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="age" placeholder="Age"
                                            min="0" max="120" value="{{ old('age') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Demographics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Sex <span class="text-danger">*</span></label>
                                        <select name="sex" class="form-select" required>
                                            <option value="">Select Sex</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="prefer_not_to_say">Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                                        <select name="civil_status" class="form-select" required>
                                            <option value="">Select Civil Status</option>
                                            <option value="SINGLE">SINGLE</option>
                                            <option value="MARRIED">MARRIED</option>
                                            <option value="WIDOW/ER">WIDOW/ER</option>
                                            <option value="SEPARATED">SEPARATED</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nationality</label>
                                        <input type="text" class="form-control" name="nationality"
                                            placeholder="Nationality" value="Filipino" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mobile"
                                            placeholder="09XX XXX XXXX" value="{{ old('mobile') }}" maxlength="11"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="your@email.com"
                                            value="{{ old('email', auth()->user()->email) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Parents' Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Mother's Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mothers_name"
                                            placeholder="Mother's Name" value="{{ old('mothers_name') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Father's Full Name</label>
                                        <input type="text" class="form-control" name="fathers_name"
                                            placeholder="Father's Name" value="{{ old('fathers_name') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" id="back-to-step-1">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary px-4" id="next-to-step-3">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Address & Location -->
                    <div id="step-3" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 3: Address & Location</h4>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Current Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Region <span class="text-danger">*</span></label>
                                        <select id="region" class="form-select" required></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <select id="province" class="form-select" disabled required></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">City/Municipality <span
                                                class="text-danger">*</span></label>
                                        <select id="city" class="form-select" disabled required></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Barangay <span class="text-danger">*</span></label>
                                        <select id="barangay" class="form-select" disabled required></select>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Street/Purok/Subdivision</label>
                                        <input type="text" class="form-control" name="street_address"
                                            placeholder="Number, Street, Purok" value="{{ old('street_address') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">District</label>
                                        <input type="text" class="form-control" name="district"
                                            placeholder="District" value="{{ old('district') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control" name="zip_code"
                                            placeholder="Zip Code" value="{{ old('zip_code') }}">
                                    </div>
                                </div>

                                <!-- Hidden inputs for PSGC codes -->
                                <input type="hidden" name="region_code" id="region_code"
                                    value="{{ old('region_code') }}">
                                <input type="hidden" name="region_name" id="region_name"
                                    value="{{ old('region_name') }}">
                                <input type="hidden" name="province_code" id="province_code"
                                    value="{{ old('province_code') }}">
                                <input type="hidden" name="province_name" id="province_name"
                                    value="{{ old('province_name') }}">
                                <input type="hidden" name="city_code" id="city_code" value="{{ old('city_code') }}">
                                <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name') }}">
                                <input type="hidden" name="barangay_code" id="barangay_code"
                                    value="{{ old('barangay_code') }}">
                                <input type="hidden" name="barangay_name" id="barangay_name"
                                    value="{{ old('barangay_name') }}">
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Birthplace Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">City/Municipality <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace_city"
                                            placeholder="City/Municipality" value="{{ old('birthplace_city') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace_province"
                                            placeholder="Province" value="{{ old('birthplace_province') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Region <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace_region"
                                            placeholder="Region" value="{{ old('birthplace_region') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Parent/Guardian Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Parent/Guardian Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="parent_guardian_name"
                                            placeholder="Parent/Guardian Name" value="{{ old('parent_guardian_name') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <label class="form-label">Complete Permanent Mailing Address <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_street"
                                            placeholder="Number, Street" value="{{ old('parent_guardian_street') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_barangay"
                                            placeholder="Barangay" value="{{ old('parent_guardian_barangay') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_district"
                                            placeholder="District" value="{{ old('parent_guardian_district') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_city"
                                            placeholder="City/Municipality" value="{{ old('parent_guardian_city') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_province"
                                            placeholder="Province" value="{{ old('parent_guardian_province') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_region"
                                            placeholder="Region" value="{{ old('parent_guardian_region') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" id="back-to-step-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary px-4" id="next-to-step-4">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: Education & Professional Background -->
                    <div id="step-4" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 4: Education & Professional Background</h4>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Educational Attainment</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Highest Educational Attainment <span
                                                class="text-danger">*</span></label>
                                        <select name="highest_educational_attainment" class="form-select" required>
                                            <option value="">Select Educational Attainment</option>
                                            <option value="ELEMENTARY GRADUATE">ELEMENTARY GRADUATE</option>
                                            <option value="HIGH SCHOOL GRADUATE">HIGH SCHOOL GRADUATE</option>
                                            <option value="TVET GRADUATE">TVET GRADUATE</option>
                                            <option value="COLLEGE LEVEL">COLLEGE LEVEL</option>
                                            <option value="COLLEGE GRADUATE">COLLEGE GRADUATE</option>
                                            <option value="MASTER'S DEGREE">MASTER'S DEGREE</option>
                                            <option value="DOCTORAL DEGREE">DOCTORAL DEGREE</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Employment Status <span
                                                class="text-danger">*</span></label>
                                        <select name="employment_status" class="form-select" required>
                                            <option value="">Select Employment Status</option>
                                            <option value="CASUAL">CASUAL</option>
                                            <option value="JOB ORDER">JOB ORDER</option>
                                            <option value="PROBATIONARY">PROBATIONARY</option>
                                            <option value="PERMANENT">PERMANENT</option>
                                            <option value="SELF-EMPLOYED">SELF-EMPLOYED</option>
                                            <option value="OFW">OFW</option>
                                            <option value="NONE">NONE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Work Experience Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Work Experience</h5>
                                <button type="button" id="add-work" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Work Experience
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="work-experiences"></div>
                                <small class="text-muted">Add at least one work experience if applicable</small>
                            </div>
                        </div>

                        <!-- Trainings Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Other Trainings / Seminars</h5>
                                <button type="button" id="add-training" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Training
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="trainings"></div>
                            </div>
                        </div>

                        <!-- Licensure Exams Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Licensure Examinations</h5>
                                <button type="button" id="add-licensure" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Licensure Exam
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="licensure-exams"></div>
                            </div>
                        </div>

                        <!-- Competency Assessments Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Competency Assessments</h5>
                                <button type="button" id="add-competency" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Competency Assessment
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="competency-assessments"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" id="back-to-step-3">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary px-4" id="next-to-step-5">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 5: Additional Details -->
                    <div id="step-5" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 5: Additional Details</h4>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Employment Before Training</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Employment Status Before Training</label>
                                        <select name="employment_before_training_status" class="form-select"
                                            id="emp_before_status">
                                            <option value="">Select Employment Status</option>
                                            <option value="wage-employed">Wage-Employed</option>
                                            <option value="underemployed">Underemployed</option>
                                            <option value="self-employed">Self-Employed</option>
                                            <option value="unemployed">Unemployed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="emp_type_wrapper" style="display: none;">
                                        <label class="form-label">Employment Type (if applicable)</label>
                                        <select name="employment_before_training_type" class="form-select">
                                            <option value="">Select Employment Type</option>
                                            <option value="regular">Regular</option>
                                            <option value="casual">Casual</option>
                                            <option value="job order">Job Order</option>
                                            <option value="probationary">Probationary</option>
                                            <option value="permanent">Permanent</option>
                                            <option value="contractual">Contractual</option>
                                            <option value="temporary">Temporary</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Educational Attainment Before Training</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <select name="educational_attainment_before_training" class="form-select">
                                            <option value="">Select Educational Attainment</option>
                                            <option value="no grade completed">No Grade Completed</option>
                                            <option value="elementary undergraduate">Elementary Undergraduate</option>
                                            <option value="elementary graduate">Elementary Graduate</option>
                                            <option value="high school undergraduate">High School Undergraduate</option>
                                            <option value="high school graduate">High School Graduate</option>
                                            <option value="junior high (k-12)">Junior High (K-12)</option>
                                            <option value="senior high (k-12)">Senior High (K-12)</option>
                                            <option value="post-secondary non-tertiary/technical vocational undergraduate">
                                                Post-Secondary Non-Tertiary/Technical Vocational Undergraduate</option>
                                            <option value="post-secondary non-tertiary/technical vocational graduate">
                                                Post-Secondary Non-Tertiary/Technical Vocational Graduate</option>
                                            <option value="college undergraduate">College Undergraduate</option>
                                            <option value="college graduate">College Graduate</option>
                                            <option value="masteral">Masteral</option>
                                            <option value="doctorate">Doctorate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Learner/Trainee/Student Classification</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-muted d-block mb-3">Select all that apply</small>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="4ps_beneficiary" id="lc1">
                                            <label class="form-check-label" for="lc1">4Ps Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="displaced_workers" id="lc2">
                                            <label class="form-check-label" for="lc2">Displaced Workers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="afp_pnp_wounded" id="lc3">
                                            <label class="form-check-label" for="lc3">Family Members of AFP/PNP
                                                Wounded-in-Action</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="industry_workers" id="lc4">
                                            <label class="form-check-label" for="lc4">Industry Workers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="out_of_school_youth"
                                                id="lc5">
                                            <label class="form-check-label" for="lc5">Out-of-School-Youth</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="rebel_returnees" id="lc6">
                                            <label class="form-check-label" for="lc6">Rebel Returnees/Decommissioned
                                                Combatants</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="tesda_alumni" id="lc7">
                                            <label class="form-check-label" for="lc7">TESDA Alumni</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="disaster_victims" id="lc8">
                                            <label class="form-check-label" for="lc8">Victim of Natural Disasters and
                                                Calamities</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="agrarian_reform" id="lc9">
                                            <label class="form-check-label" for="lc9">Agrarian Reform
                                                Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="drug_dependents" id="lc10">
                                            <label class="form-check-label" for="lc10">Drug Dependents
                                                Surrenderees</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="farmers_fishermen" id="lc11">
                                            <label class="form-check-label" for="lc11">Farmers and Fishermen</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="inmates_detainees" id="lc12">
                                            <label class="form-check-label" for="lc12">Inmates and Detainees</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="ofw_dependent" id="lc13">
                                            <label class="form-check-label" for="lc13">OFW Dependent</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="returning_ofw" id="lc14">
                                            <label class="form-check-label" for="lc14">Returning/Repatriated
                                                OFW</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="tvet_trainers" id="lc15">
                                            <label class="form-check-label" for="lc15">TVET Trainers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="wounded_afp_pnp" id="lc16">
                                            <label class="form-check-label" for="lc16">Wounded-in-Action AFP & PNP
                                                Personnel</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="balik_probinsya" id="lc17">
                                            <label class="form-check-label" for="lc17">Balik Probinsya</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="afp_pnp_killed" id="lc18">
                                            <label class="form-check-label" for="lc18">Family Members of AFP/PNP
                                                Killed-in-Action</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="indigenous_people" id="lc19">
                                            <label class="form-check-label" for="lc19">Indigenous People & Cultural
                                                Communities</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="milf_beneficiary" id="lc20">
                                            <label class="form-check-label" for="lc20">MILF Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="rcef_resp" id="lc21">
                                            <label class="form-check-label" for="lc21">RCEF-RESP</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="student" id="lc22">
                                            <label class="form-check-label" for="lc22">Student</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="uniformed_personnel"
                                                id="lc23">
                                            <label class="form-check-label" for="lc23">Uniformed Personnel</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="others" id="lc24">
                                            <label class="form-check-label" for="lc24">Others</label>
                                        </div>
                                        <div class="mt-2" id="others-input-wrapper" style="display: none;">
                                            <input type="text" class="form-control"
                                                name="learner_classification_other" id="others_text_input"
                                                placeholder="Please specify" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4" id="scholarship-card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Scholarship Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">If Scholar, What Type of Scholarship Package?</label>
                                        <input type="text" class="form-control" name="scholarship_type"
                                            id="scholarship_type" placeholder="e.g., TWSP, PESFA, STEP, others" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Privacy Consent and Disclaimer</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <small>I hereby attest that I have read and understood the Privacy Notice of TESDA
                                        through its website and thereby giving my consent in the processing of my personal
                                        information indicated in this Learners Profile. The processing includes
                                        scholarships,
                                        employment, survey, and all other related TESDA programs that may be beneficial to
                                        my
                                        qualifications.</small>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privacy_consent"
                                        value="1" id="privacy_consent" required>
                                    <label class="form-check-label fw-bold" for="privacy_consent">
                                        I Agree to the Privacy Consent <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" id="back-to-step-4">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                Submit Application <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal (Only for first step) -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this application? All entered data will be lost.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Continue</button>
                    <button type="button" class="btn btn-danger" id="confirm-cancel">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== INITIALIZATION ==========
            const applicationType = sessionStorage.getItem('application_type');
            const programName = sessionStorage.getItem('program_name');
            const form = document.getElementById('application-form');
            const banner = document.getElementById('application-info-banner');
            const bannerMessage = document.getElementById('banner-message');

            // Define steps array
            const steps = ['step-0', 'step-1', 'step-2', 'step-3', 'step-4', 'step-5'];
            let currentStep = 0;

            // Add hidden field for application type
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'application_type';
            hiddenInput.value = applicationType || 'Assessment Only';
            form.appendChild(hiddenInput);

            // ========== APPLICATION TYPE HANDLING ==========
            if (applicationType === 'TWSP' && programName) {
                // Pre-fill and lock program selection
                const programSelect = document.getElementById('title_of_assessment_applied_for');
                if (programSelect) {
                    programSelect.value = programName;
                    programSelect.setAttribute('readonly', true);
                    programSelect.style.pointerEvents = 'none';
                    programSelect.style.backgroundColor = '#e9ecef';
                }

                // Show Step 0
                showStep(0);

                // Show banner
                banner.style.display = 'block';
                bannerMessage.innerHTML = `<strong>TWSP Application:</strong> ${programName}`;

                // Set scholarship type
                document.getElementById('scholarship_type').value = 'TWSP';
            } else {
                // Assessment Only - start at Step 1
                showStep(1);
                banner.style.display = 'none';
                document.getElementById('scholarship_type').value = '';
                const scholarshipCard = document.getElementById('scholarship-card');
                if (scholarshipCard) {
                    scholarshipCard.style.display = 'none';
                }
            }

            // Close banner
            document.getElementById('close-banner')?.addEventListener('click', function() {
                banner.style.display = 'none';
            });

            // ========== STEP NAVIGATION FUNCTIONS ==========
            function showStep(stepNumber) {
                console.log('Showing step:', stepNumber);

                // Hide all steps
                steps.forEach(step => {
                    const element = document.getElementById(step);
                    if (element) element.style.display = 'none';
                });

                // Show current step
                const currentStepElement = document.getElementById(`step-${stepNumber}`);
                if (currentStepElement) {
                    currentStepElement.style.display = 'block';
                    currentStep = stepNumber;
                }

                // Update progress bar
                updateProgressBar(stepNumber);

                // Update navigation buttons
                updateNavigationButtons(stepNumber);

                // Scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            function updateProgressBar(stepNumber) {
                const totalSteps = applicationType === 'TWSP' ? 6 : 5;
                const currentStepNumber = applicationType === 'TWSP' ? stepNumber + 1 : stepNumber;
                const progressPercentage = (currentStepNumber / totalSteps) * 100;

                const progressBar = document.getElementById('progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${progressPercentage}%`;
                    progressBar.setAttribute('aria-valuenow', progressPercentage);
                    progressBar.textContent = `Step ${currentStepNumber} of ${totalSteps}`;
                }
            }

            function updateNavigationButtons(stepNumber) {
                // Handle Back to Documents button visibility
                const backToStep0 = document.getElementById('back-to-step-0');
                if (backToStep0) {
                    if (applicationType === 'TWSP' && stepNumber === 1) {
                        backToStep0.style.display = 'inline-block';
                    } else {
                        backToStep0.style.display = 'none';
                    }
                }
            }

            // ========== MIDDLE NAME TO INITIAL ==========
            const middleNameInput = document.querySelector('input[name="middlename"]');
            const middleInitialInput = document.querySelector('input[name="middleinitial"]');

            if (middleNameInput && middleInitialInput) {
                middleNameInput.addEventListener('input', function() {
                    const middleName = this.value.trim();
                    middleInitialInput.value = middleName.length > 0 ? middleName.charAt(0).toUpperCase() +
                        '.' : '';
                });

                middleNameInput.addEventListener('blur', function() {
                    const middleName = this.value.trim();
                    middleInitialInput.value = middleName.length > 0 ? middleName.charAt(0).toUpperCase() :
                        '';
                });
            }

            // ========== NAVIGATION EVENT LISTENERS ==========

            // Step 0 Navigation
            document.getElementById('next-from-step-0')?.addEventListener('click', function() {
                if (validateStep0()) {
                    showStep(1);
                    if (applicationType === 'TWSP') {
                        document.getElementById('cancel-step-1').style.display = 'none';
                    }
                }
            });

            // Back to Step 0 from Step 1
            document.getElementById('back-to-step-0')?.addEventListener('click', function() {
                showStep(0);
            });

            // Step 1 to Step 2
            document.getElementById('next-to-step-2')?.addEventListener('click', function() {
                if (validateStep1()) {
                    showStep(2);
                }
            });

            // Back to Step 1
            document.getElementById('back-to-step-1')?.addEventListener('click', function() {
                showStep(1);
            });

            // Step 2 to Step 3
            document.getElementById('next-to-step-3')?.addEventListener('click', function() {
                if (validateStep2()) {
                    showStep(3);
                }
            });

            // Back to Step 2
            document.getElementById('back-to-step-2')?.addEventListener('click', function() {
                showStep(2);
            });

            // Step 3 to Step 4
            document.getElementById('next-to-step-4')?.addEventListener('click', function() {
                if (validateStep3()) {
                    showStep(4);
                }
            });

            // Back to Step 3
            document.getElementById('back-to-step-3')?.addEventListener('click', function() {
                showStep(3);
            });

            // Step 4 to Step 5
            document.getElementById('next-to-step-5')?.addEventListener('click', function() {
                if (validateStep4()) {
                    showStep(5);
                }
            });

            // Back to Step 4
            document.getElementById('back-to-step-4')?.addEventListener('click', function() {
                showStep(4);
            });

            // ========== CANCEL BUTTON HANDLERS (Only for first steps) ==========
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));

            // Cancel from Step 0 (TWSP)
            document.getElementById('cancel-step-0')?.addEventListener('click', function() {
                cancelModal.show();
            });

            // Cancel from Step 1 (Assessment Only or TWSP Step 1)
            document.getElementById('cancel-step-1')?.addEventListener('click', function() {
                cancelModal.show();
            });

            document.getElementById('confirm-cancel').addEventListener('click', function() {
                window.location.href = '{{ route('applicant.dashboard') }}'; // Adjust this route as needed
            });

            // ========== VALIDATION FUNCTIONS ==========
            function validateStep0() {
                const requiredFiles = [{
                        id: 'psa_birth_certificate',
                        name: 'PSA Birth Certificate'
                    },
                    {
                        id: 'high_school_document',
                        name: 'High School Document'
                    },
                    {
                        id: 'id_pictures_1x1',
                        name: '1x1 ID Pictures',
                        min: 1,
                        max: 4
                    },
                    {
                        id: 'id_pictures_passport',
                        name: 'Passport Size Pictures',
                        min: 1,
                        max: 4
                    },
                    {
                        id: 'government_school_id',
                        name: 'Government/School ID',
                        min: 1,
                        max: 2
                    },
                    {
                        id: 'certificate_of_indigency',
                        name: 'Certificate of Indigency'
                    }
                ];

                let isValid = true;
                let errors = [];

                requiredFiles.forEach(item => {
                    const field = document.getElementById(item.id);
                    if (field) {
                        if (field.files.length === 0) {
                            isValid = false;
                            field.classList.add('is-invalid');
                            errors.push(`${item.name} is required`);
                        } else {
                            field.classList.remove('is-invalid');

                            if (item.min && field.files.length < item.min) {
                                isValid = false;
                                errors.push(`${item.name}: Please upload at least ${item.min} file(s)`);
                            }
                            if (item.max && field.files.length > item.max) {
                                isValid = false;
                                errors.push(`${item.name}: Maximum ${item.max} file(s) allowed`);
                            }
                        }
                    }
                });

                if (!isValid) {
                    alert('Please check the following:\n\n' + errors.join('\n'));
                }

                return isValid;
            }

            function validateStep1() {
                const programSelect = document.getElementById('title_of_assessment_applied_for');
                const photoInput = document.getElementById('photo');

                if (!programSelect.value) {
                    programSelect.classList.add('is-invalid');
                    programSelect.focus();
                    alert('Please select a program');
                    return false;
                } else {
                    programSelect.classList.remove('is-invalid');
                }

                if (!photoInput.files.length) {
                    photoInput.classList.add('is-invalid');
                    photoInput.focus();
                    alert('Please upload your photo');
                    return false;
                } else {
                    photoInput.classList.remove('is-invalid');
                }

                return true;
            }

            function validateStep2() {
                const requiredFields = document.querySelectorAll('#step-2 [required]');
                const ageInput = document.querySelector('input[name="age"]');
                const age = parseInt(ageInput.value);

                if (!ageInput.value || age <= 0) {
                    ageInput.classList.add('is-invalid');
                    ageInput.focus();
                    ageInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    alert('Please enter a valid age. Age must be greater than 0.');
                    return false;
                } else {
                    ageInput.classList.remove('is-invalid');
                }

                return validateRequiredFields(requiredFields,
                    'Please fill in all required fields in Personal Information');
            }

            function validateStep3() {
                const requiredFields = document.querySelectorAll('#step-3 [required]');
                return validateRequiredFields(requiredFields,
                    'Please fill in all required fields in Address section');
            }

            function validateStep4() {
                const requiredFields = document.querySelectorAll('#step-4 [required]');
                const sections = ['#work-experiences', '#trainings', '#licensure-exams', '#competency-assessments'];

                for (let section of sections) {
                    const rows = document.querySelectorAll(`${section} .border`);

                    for (let row of rows) {
                        const inputs = row.querySelectorAll('input');
                        const isEmpty = Array.from(inputs).every(input => !input.value.trim());

                        if (isEmpty) {
                            row.style.border = '2px solid red';
                            alert('Please remove empty entries or fill in at least one field');
                            return false;
                        } else {
                            row.style.border = '';
                        }
                    }
                }

                return validateRequiredFields(requiredFields,
                    'Please fill in all required fields in Education section');
            }

            function validateRequiredFields(fields, message) {
                let isValid = true;
                let firstInvalid = null;

                fields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (!firstInvalid) firstInvalid = field;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid && firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    alert(message);
                }

                return isValid;
            }

            // ========== AGE CALCULATION ==========
            const birthdateInput = document.querySelector('input[name="birthdate"]');
            const ageInput = document.querySelector('input[name="age"]');

            if (birthdateInput) {
                birthdateInput.addEventListener('change', function() {
                    if (this.value) {
                        const birthDate = new Date(this.value);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const monthDiff = today.getMonth() - birthDate.getMonth();

                        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }

                        age = Math.max(0, age);
                        ageInput.value = age;

                        if (age === 0) {
                            let warningDiv = document.getElementById('age-warning');
                            if (!warningDiv) {
                                warningDiv = document.createElement('div');
                                warningDiv.className = 'alert alert-warning mt-2';
                                warningDiv.id = 'age-warning';
                                warningDiv.innerHTML =
                                    '<small><i class="fas fa-exclamation-triangle"></i> Please verify birthdate - age appears to be 0</small>';
                                birthdateInput.parentElement.appendChild(warningDiv);
                            }
                        } else {
                            const existingWarning = document.getElementById('age-warning');
                            if (existingWarning) existingWarning.remove();
                        }
                    }
                });
            }

            // ========== EMPLOYMENT TYPE TOGGLE ==========
            const empBeforeStatus = document.getElementById('emp_before_status');
            const empTypeWrapper = document.getElementById('emp_type_wrapper');

            if (empBeforeStatus) {
                empBeforeStatus.addEventListener('change', function() {
                    empTypeWrapper.style.display = (this.value === 'wage-employed' || this.value ===
                        'underemployed') ? 'block' : 'none';
                });
            }

            // ========== "OTHERS" CHECKBOX HANDLER ==========
            const othersCheckbox = document.getElementById('lc24');
            const othersInputWrapper = document.getElementById('others-input-wrapper');
            const othersTextInput = document.getElementById('others_text_input');

            if (othersCheckbox) {
                othersCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        othersInputWrapper.style.display = 'block';
                        othersTextInput.disabled = false;
                    } else {
                        othersInputWrapper.style.display = 'none';
                        othersTextInput.disabled = true;
                        othersTextInput.value = '';
                    }
                });
            }

            // ========== PHOTO PREVIEW ==========
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('photo-preview');

            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            photoPreview.src = e.target.result;
                            photoPreview.style.display = 'inline-block';
                        };
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        photoPreview.style.display = 'none';
                    }
                });
            }

            // ========== FILE PREVIEW SETUP ==========
            function setupFilePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                if (!input || !preview) return;

                input.addEventListener('change', function() {
                    preview.innerHTML = '';

                    if (this.files && this.files.length > 0) {
                        const fileList = document.createElement('div');
                        fileList.className = 'alert alert-success py-2';

                        let fileNames = Array.from(this.files).map(f => f.name).join(', ');
                        fileList.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>${this.files.length} file(s) selected:</strong><br>
                            <small>${fileNames}</small>
                        `;

                        preview.appendChild(fileList);
                    }
                });
            }

            // Setup all file previews
            setupFilePreview('psa_birth_certificate', 'preview_psa_birth_certificate');
            setupFilePreview('psa_marriage_contract', 'preview_psa_marriage_contract');
            setupFilePreview('high_school_document', 'preview_high_school_document');
            setupFilePreview('id_pictures_1x1', 'preview_id_pictures_1x1');
            setupFilePreview('id_pictures_passport', 'preview_id_pictures_passport');
            setupFilePreview('government_school_id', 'preview_government_school_id');
            setupFilePreview('certificate_of_indigency', 'preview_certificate_of_indigency');

            // ========== PSGC API FUNCTIONS ==========
            const PSGC_BASE = 'https://psgc.gitlab.io/api';

            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');

            const regionCode = document.getElementById('region_code');
            const regionName = document.getElementById('region_name');
            const provinceCode = document.getElementById('province_code');
            const provinceName = document.getElementById('province_name');
            const cityCode = document.getElementById('city_code');
            const cityName = document.getElementById('city_name');
            const barangayCode = document.getElementById('barangay_code');
            const barangayName = document.getElementById('barangay_name');

            function setOptions(select, items, labelKey = 'name', valueKey = 'code') {
                select.innerHTML = '<option value="">Select...</option>';
                for (const item of items) {
                    const opt = document.createElement('option');
                    opt.value = item[valueKey];
                    opt.textContent = item[labelKey];
                    opt.dataset.name = item[labelKey];
                    select.appendChild(opt);
                }
                select.disabled = false;
            }

            async function loadRegions() {
                try {
                    const res = await fetch(`${PSGC_BASE}/regions/`);
                    const data = await res.json();
                    setOptions(regionSelect, data, 'regionName', 'code');
                } catch (error) {
                    console.error('Error loading regions:', error);
                }
            }

            async function loadProvinces(regionCodeVal) {
                provinceSelect.disabled = true;
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                provinceSelect.innerHTML = '';
                citySelect.innerHTML = '';
                barangaySelect.innerHTML = '';

                try {
                    const res = await fetch(`${PSGC_BASE}/regions/${regionCodeVal}/provinces/`);
                    const data = await res.json();
                    setOptions(provinceSelect, data, 'name', 'code');
                } catch (error) {
                    console.error('Error loading provinces:', error);
                }
            }

            async function loadCities(provinceCodeVal) {
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                citySelect.innerHTML = '';
                barangaySelect.innerHTML = '';

                try {
                    const res = await fetch(`${PSGC_BASE}/provinces/${provinceCodeVal}/cities-municipalities/`);
                    const data = await res.json();
                    setOptions(citySelect, data, 'name', 'code');
                } catch (error) {
                    console.error('Error loading cities:', error);
                }
            }

            async function loadBarangays(cityCodeVal) {
                barangaySelect.disabled = true;
                barangaySelect.innerHTML = '';

                try {
                    const res = await fetch(`${PSGC_BASE}/cities-municipalities/${cityCodeVal}/barangays/`);
                    const data = await res.json();
                    setOptions(barangaySelect, data, 'name', 'code');
                } catch (error) {
                    console.error('Error loading barangays:', error);
                }
            }

            // Bind events
            if (regionSelect) {
                regionSelect.addEventListener('change', e => {
                    const opt = regionSelect.selectedOptions[0];
                    regionCode.value = opt?.value || '';
                    regionName.value = opt?.dataset.name || '';
                    if (opt?.value) loadProvinces(opt.value);
                });
            }

            if (provinceSelect) {
                provinceSelect.addEventListener('change', e => {
                    const opt = provinceSelect.selectedOptions[0];
                    provinceCode.value = opt?.value || '';
                    provinceName.value = opt?.dataset.name || '';
                    if (opt?.value) loadCities(opt.value);
                });
            }

            if (citySelect) {
                citySelect.addEventListener('change', e => {
                    const opt = citySelect.selectedOptions[0];
                    cityCode.value = opt?.value || '';
                    cityName.value = opt?.dataset.name || '';
                    if (opt?.value) loadBarangays(opt.value);
                });
            }

            if (barangaySelect) {
                barangaySelect.addEventListener('change', e => {
                    const opt = barangaySelect.selectedOptions[0];
                    barangayCode.value = opt?.value || '';
                    barangayName.value = opt?.dataset.name || '';
                });
            }

            // Initialize regions
            loadRegions();

            // Restore address values if they exist (from old() helper)
            @if (old('region_code'))
                setTimeout(async () => {
                    const oldRegionCode = "{{ old('region_code') }}";
                    const oldProvinceCode = "{{ old('province_code') }}";
                    const oldCityCode = "{{ old('city_code') }}";
                    const oldBarangayCode = "{{ old('barangay_code') }}";

                    // Set region
                    regionSelect.value = oldRegionCode;
                    const regionOpt = regionSelect.selectedOptions[0];
                    if (regionOpt) {
                        regionCode.value = regionOpt.value;
                        regionName.value = regionOpt.dataset.name;
                    }

                    if (oldProvinceCode) {
                        await loadProvinces(oldRegionCode);
                        setTimeout(() => {
                            provinceSelect.value = oldProvinceCode;
                            const provinceOpt = provinceSelect.selectedOptions[0];
                            if (provinceOpt) {
                                provinceCode.value = provinceOpt.value;
                                provinceName.value = provinceOpt.dataset.name;
                            }

                            if (oldCityCode) {
                                loadCities(oldProvinceCode).then(() => {
                                    setTimeout(() => {
                                        citySelect.value = oldCityCode;
                                        const cityOpt = citySelect
                                            .selectedOptions[0];
                                        if (cityOpt) {
                                            cityCode.value = cityOpt.value;
                                            cityName.value = cityOpt.dataset
                                                .name;
                                        }

                                        if (oldBarangayCode) {
                                            loadBarangays(oldCityCode).then(
                                                () => {
                                                    setTimeout(() => {
                                                        barangaySelect
                                                            .value =
                                                            oldBarangayCode;
                                                        const
                                                            barangayOpt =
                                                            barangaySelect
                                                            .selectedOptions[
                                                                0];
                                                        if (
                                                            barangayOpt
                                                            ) {
                                                            barangayCode
                                                                .value =
                                                                barangayOpt
                                                                .value;
                                                            barangayName
                                                                .value =
                                                                barangayOpt
                                                                .dataset
                                                                .name;
                                                        }
                                                    }, 300);
                                                });
                                        }
                                    }, 300);
                                });
                            }
                        }, 300);
                    }
                }, 500);
            @endif

            // ========== DYNAMIC SECTION FUNCTIONS ==========

            // Work Experience
            function addWorkExperienceRow() {
                const wrap = document.getElementById('work-experiences');
                const idx = wrap.children.length;
                const div = document.createElement('div');
                div.className = 'border p-3 mb-2 rounded';
                div.innerHTML = `
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Work Experience #${idx + 1}</label>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Company Name</label>
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][company_name]" placeholder="Enter company name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Position</label>
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][position]" placeholder="Enter position">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Date From</label>
                            <input type="date" class="form-control form-control-sm" name="work_experiences[${idx}][date_from]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Date To</label>
                            <input type="date" class="form-control form-control-sm" name="work_experiences[${idx}][date_to]">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Monthly Salary</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="work_experiences[${idx}][monthly_salary]" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Appointment Status</label>
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][appointment_status]" placeholder="e.g., Permanent, Casual">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label small">Years of Experience</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" name="work_experiences[${idx}][years_experience]" placeholder="No. of years">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                wrap.appendChild(div);
            }
            document.getElementById('add-work')?.addEventListener('click', addWorkExperienceRow);

            // Training
            function addTrainingRow() {
                const wrap = document.getElementById('trainings');
                const idx = wrap.children.length;
                const div = document.createElement('div');
                div.className = 'border p-3 mb-2 rounded';
                div.innerHTML = `
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Training/Seminar #${idx + 1}</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Title of Training/Seminar</label>
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][title]" placeholder="Enter title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Venue</label>
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][venue]" placeholder="Enter venue">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Date From</label>
                            <input type="date" class="form-control form-control-sm" name="trainings[${idx}][date_from]">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Date To</label>
                            <input type="date" class="form-control form-control-sm" name="trainings[${idx}][date_to]">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Number of Hours</label>
                            <input type="number" class="form-control form-control-sm" name="trainings[${idx}][hours]" placeholder="Hours">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Conducted By</label>
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][conducted_by]" placeholder="Organization/Institution">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                wrap.appendChild(div);
            }
            document.getElementById('add-training')?.addEventListener('click', addTrainingRow);

            // Licensure Exam
            function addLicensureRow() {
                const wrap = document.getElementById('licensure-exams');
                const idx = wrap.children.length;
                const div = document.createElement('div');
                div.className = 'border p-3 mb-2 rounded';
                div.innerHTML = `
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Licensure Examination #${idx + 1}</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Title of Examination</label>
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][title]" placeholder="e.g., Licensure Exam for Teachers">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Year Taken</label>
                            <input type="number" class="form-control form-control-sm" name="licensure_exams[${idx}][year_taken]" placeholder="YYYY" min="1900" max="2099">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Examination Venue</label>
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][exam_venue]" placeholder="City/Municipality">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Rating (%)</label>
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][rating]" placeholder="e.g., 85.5%">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Remarks</label>
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][remarks]" placeholder="e.g., Passed, Failed">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Expiry Date (if applicable)</label>
                            <input type="date" class="form-control form-control-sm" name="licensure_exams[${idx}][expiry_date]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                wrap.appendChild(div);
            }
            document.getElementById('add-licensure')?.addEventListener('click', addLicensureRow);

            // Competency Assessment
            function addCompetencyRow() {
                const wrap = document.getElementById('competency-assessments');
                const idx = wrap.children.length;
                const div = document.createElement('div');
                div.className = 'border p-3 mb-2 rounded';
                div.innerHTML = `
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Competency Assessment #${idx + 1}</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Title of Qualification</label>
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][title]" placeholder="e.g., BOOKKEEPING NC III">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Qualification Level</label>
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][qualification_level]" placeholder="e.g., NC III, NC II">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Industry Sector</label>
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][industry_sector]" placeholder="e.g., Tourism, IT">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Certificate Number</label>
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][certificate_number]" placeholder="Certificate #">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Date of Issuance</label>
                            <input type="date" class="form-control form-control-sm" name="competency_assessments[${idx}][date_of_issuance]">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Expiration Date</label>
                            <input type="date" class="form-control form-control-sm" name="competency_assessments[${idx}][expiration_date]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                wrap.appendChild(div);
            }
            document.getElementById('add-competency')?.addEventListener('click', addCompetencyRow);
        });
    </script>
@endpush
