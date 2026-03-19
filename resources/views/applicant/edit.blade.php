<!-- resources/views/applicant/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Update Application</h3>
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

                {{-- <!-- Application Type Banner -->
                @if ($application->application_type === 'TWSP')
                    <div id="application-info-banner" class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>TWSP Application:</strong>
                                {{ $application->title_of_assessment_applied_for }}</span>
                        </div>
                    </div>
                @endif --}}

                <form method="POST" action="{{ route('applicant.applications.update', $application) }}"
                    enctype="multipart/form-data" id="application-form">
                    @csrf
                    @method('PUT')

                    @if ($application->application_type === 'TWSP')
                        <!-- STEP 0: TWSP Documents (Only for TWSP applications) -->
                        <div id="step-0" class="form-step"
                            style="{{ $application->application_type === 'TWSP' ? 'display: block;' : 'display: none;' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>TWSP Requirements:</strong> Update your documents if needed. Leave blank to keep
                                existing files.
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
                                        <small class="text-muted">PDF, JPG, PNG (Max: 5MB). Leave empty to keep current
                                            file.</small>
                                        @if ($application->twspDocument && $application->twspDocument->psa_birth_certificate)
                                            <div class="mt-2">

                                                <small class="text-muted">Leave empty to keep this file, or upload new file
                                                    to replace</small>

                                                {{-- Image Preview --}}
                                                @if (in_array(strtolower(pathinfo($application->twspDocument->psa_birth_certificate, PATHINFO_EXTENSION)), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                    ]))
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($application->twspDocument->psa_birth_certificate) }}"
                                                            alt="Current PSA Birth Certificate" class="img-thumbnail"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($application->twspDocument->psa_birth_certificate) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> View PDF
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
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
                                        @if ($application->twspDocument && $application->twspDocument->psa_marriage_contract)
                                            <div class="mt-2">
                                                <small class="text-muted">Leave empty to keep this file, or upload new file
                                                    to replace</small>
                                                {{-- Image Preview --}}
                                                @if (in_array(strtolower(pathinfo($application->twspDocument->psa_marriage_contract, PATHINFO_EXTENSION)), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                    ]))
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($application->twspDocument->psa_marriage_contract) }}"
                                                            alt="Current PSA Marriage Contract" class="img-thumbnail"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($application->twspDocument->psa_marriage_contract) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> View PDF
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
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
                                        @if ($application->twspDocument && $application->twspDocument->high_school_document)
                                            <div class="mt-2">
                                                <small class="text-muted">Leave empty to keep this file, or upload new file
                                                    to replace</small>

                                                {{-- Image Preview --}}
                                                @if (in_array(strtolower(pathinfo($application->twspDocument->high_school_document, PATHINFO_EXTENSION)), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                    ]))
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($application->twspDocument->high_school_document) }}"
                                                            alt="Current High School Document" class="img-thumbnail"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($application->twspDocument->high_school_document) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> View PDF
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
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
                                        <input type="file" class="form-control" name="id_pictures_1x1[]"
                                            id="id_pictures_1x1" accept=".jpg,.jpeg,.png" multiple>
                                        <small class="text-muted">(Upload 1-4 files). Leave empty to keep current
                                            files.</small>
                                        @if ($application->twspDocument && $application->twspDocument->id_pictures_1x1)
                                            <div class="mt-2">
                                                {{-- Image Previews --}}
                                                <div class="mt-2 row">
                                                    <small class="text-muted">Leave empty to keep current files, or upload
                                                        new
                                                        files to replace</small>
                                                    @foreach ($application->twspDocument->id_pictures_1x1 as $file)
                                                        <div class="col-md-3 mb-2">

                                                            @if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                                <img src="{{ Storage::url($file) }}" alt="ID Picture"
                                                                    class="img-thumbnail"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                            @else
                                                                <a href="{{ Storage::url($file) }}" target="_blank"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-file"></i> View File
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        @endif
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
                                        @if ($application->twspDocument && $application->twspDocument->id_pictures_passport)
                                            <div class="mt-2">
                                                {{-- Image Previews --}}
                                                <div class="mt-2 row">
                                                    <small class="text-muted">Leave empty to keep current files, or upload
                                                        new files to replace</small>
                                                    @foreach ($application->twspDocument->id_pictures_passport as $file)
                                                        <div class="col-md-3 mb-2">
                                                            @if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                                <img src="{{ Storage::url($file) }}"
                                                                    alt="Passport Size Picture" class="img-thumbnail"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                            @else
                                                                <a href="{{ Storage::url($file) }}" target="_blank"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-file"></i> View File
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
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
                                        @if ($application->twspDocument && $application->twspDocument->government_school_id)
                                            <div class="mt-2">


                                                {{-- Image Previews --}}
                                                <div class="mt-2 row">
                                                    <small class="text-muted">Leave empty to keep current files, or upload
                                                        new files to replace</small>
                                                    @foreach ($application->twspDocument->government_school_id as $file)
                                                        <div class="col-md-3 mb-2">
                                                            @if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                                <img src="{{ Storage::url($file) }}"
                                                                    alt="Government/School ID" class="img-thumbnail"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                            @else
                                                                <a href="{{ Storage::url($file) }}" target="_blank"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-file-pdf"></i> View PDF
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
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

                                        @if ($application->twspDocument && $application->twspDocument->certificate_of_indigency)
                                            <div class="mt-2">
                                               
                                                <small class="text-muted">Leave empty to keep this file, or upload new file
                                                    to replace</small>

                                                {{-- Image Preview --}}
                                                @if (in_array(strtolower(pathinfo($application->twspDocument->certificate_of_indigency, PATHINFO_EXTENSION)), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                    ]))
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($application->twspDocument->certificate_of_indigency) }}"
                                                            alt="Current Certificate of Indigency" class="img-thumbnail"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($application->twspDocument->certificate_of_indigency) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> View PDF
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mt-2" id="preview_certificate_of_indigency"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary px-4" id="cancel-step-0">
                                    <i class="fas fa-times"></i> Cancel Update
                                </button>
                                <button type="button" class="btn btn-primary px-4" id="next-from-step-0">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- STEP 1: Program Selection & Photo -->
                    <div id="step-1" class="form-step" style="display: none;">
                        <h4 class="text-primary mb-4">Step 1: Program Selection & Photo</h4>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label class="text-primary fw-bold form-label" for="title_of_assessment_applied_for">
                                        Title of Assessment Applied For <span class="text-danger">*</span>
                                    </label>
                                    <select name="title_of_assessment_applied_for" class="form-select form-select-lg"
                                        id="title_of_assessment_applied_for" required>
                                        <option value="">Select Title of Assessment</option>
                                        <option value="BOOKKEEPING NC III"
                                            {{ old('title_of_assessment_applied_for', $application->title_of_assessment_applied_for) == 'BOOKKEEPING NC III' ? 'selected' : '' }}>
                                            BOOKKEEPING NC III</option>
                                        <option value="EVENTS MANAGEMENT SERVICES NC III"
                                            {{ old('title_of_assessment_applied_for', $application->title_of_assessment_applied_for) == 'EVENTS MANAGEMENT SERVICES NC III' ? 'selected' : '' }}>
                                            EVENTS MANAGEMENT SERVICES NC III
                                        </option>
                                        <option value="TOURISM PROMOTION SERVICES NC II"
                                            {{ old('title_of_assessment_applied_for', $application->title_of_assessment_applied_for) == 'TOURISM PROMOTION SERVICES NC II' ? 'selected' : '' }}>
                                            TOURISM PROMOTION SERVICES NC II
                                        </option>
                                        <option value="PHARMACY SERVICES NC III"
                                            {{ old('title_of_assessment_applied_for', $application->title_of_assessment_applied_for) == 'PHARMACY SERVICES NC III' ? 'selected' : '' }}>
                                            PHARMACY SERVICES NC III</option>
                                        <option value="VISUAL GRAPHIC DESIGN NC III"
                                            {{ old('title_of_assessment_applied_for', $application->title_of_assessment_applied_for) == 'VISUAL GRAPHIC DESIGN NC III' ? 'selected' : '' }}>
                                            VISUAL GRAPHIC DESIGN NC III</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label class="text-primary fw-bold" for="photo">
                                        Photo (Colored, Passport Size) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control mb-2" name="photo" id="photo"
                                        accept="image/jpeg,image/png,image/jpg">
                                    <small class="text-muted d-block mb-2">JPEG or PNG, max 2MB. Leave empty to keep
                                        current photo.</small>

                                    @if ($application->photo)
                                        <div class="mt-2 text-center">
                                            <p class="mb-1"><strong>Current Photo:</strong></p>
                                            <img src="{{ Storage::url($application->photo) }}" alt="Current Photo"
                                                class="img-thumbnail" id="current-photo"
                                                style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                        </div>
                                    @endif

                                    <div class="text-center mt-2">
                                        <img id="photo-preview" src="" alt="Preview" class="img-thumbnail"
                                            style="max-width: 150px; max-height: 150px; display: none; border: 3px solid #000000;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                @if ($application->application_type === 'TWSP')
                                    <button type="button" class="btn btn-outline-secondary px-4" id="back-to-step-0">
                                        <i class="fas fa-arrow-left"></i> Back to Documents
                                    </button>
                                @endif
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
                                            value="{{ old('surname', $application->surname) }}"
                                            style="text-transform: uppercase;" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="firstname"
                                            placeholder="First name"
                                            value="{{ old('firstname', $application->firstname) }}"
                                            style="text-transform: uppercase;" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" style="text-transform: uppercase;"
                                            name="middlename" placeholder="Middle name"
                                            value="{{ old('middlename', $application->middlename) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Middle Initial</label>
                                        <input type="text" class="form-control" style="text-transform: uppercase;"
                                            name="middleinitial" placeholder="MI"
                                            value="{{ old('middleinitial', $application->middleinitial) }}"
                                            maxlength="2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Name Extension</label>
                                        <select name="name_extension" class="form-select">
                                            <option value="">None</option>
                                            <option value="Jr."
                                                {{ old('name_extension', $application->name_extension) == 'Jr.' ? 'selected' : '' }}>
                                                Jr.</option>
                                            <option value="Sr."
                                                {{ old('name_extension', $application->name_extension) == 'Sr.' ? 'selected' : '' }}>
                                                Sr.</option>
                                            <option value="II"
                                                {{ old('name_extension', $application->name_extension) == 'II' ? 'selected' : '' }}>
                                                II</option>
                                            <option value="III"
                                                {{ old('name_extension', $application->name_extension) == 'III' ? 'selected' : '' }}>
                                                III</option>
                                            <option value="IV"
                                                {{ old('name_extension', $application->name_extension) == 'IV' ? 'selected' : '' }}>
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
                                            value="{{ old('birthdate', $application->birthdate) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Birthplace <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace"
                                            placeholder="City/Municipality"
                                            value="{{ old('birthplace', $application->birthplace) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Age <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="age" placeholder="Age"
                                            min="0" max="120" value="{{ old('age', $application->age) }}"
                                            required readonly>
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
                                            <option value="male"
                                                {{ old('sex', $application->sex) == 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female"
                                                {{ old('sex', $application->sex) == 'female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="prefer_not_to_say"
                                                {{ old('sex', $application->sex) == 'prefer_not_to_say' ? 'selected' : '' }}>
                                                Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                                        <select name="civil_status" class="form-select" required>
                                            <option value="">Select Civil Status</option>
                                            <option value="SINGLE"
                                                {{ old('civil_status', $application->civil_status) == 'SINGLE' ? 'selected' : '' }}>
                                                SINGLE</option>
                                            <option value="MARRIED"
                                                {{ old('civil_status', $application->civil_status) == 'MARRIED' ? 'selected' : '' }}>
                                                MARRIED</option>
                                            <option value="WIDOW/ER"
                                                {{ old('civil_status', $application->civil_status) == 'WIDOW/ER' ? 'selected' : '' }}>
                                                WIDOW/ER</option>
                                            <option value="SEPARATED"
                                                {{ old('civil_status', $application->civil_status) == 'SEPARATED' ? 'selected' : '' }}>
                                                SEPARATED</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nationality</label>
                                        <input type="text" class="form-control" name="nationality"
                                            placeholder="Nationality"
                                            value="{{ old('nationality', $application->nationality ?? 'Filipino') }}"
                                            readonly>
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
                                            placeholder="09XX XXX XXXX" value="{{ old('mobile', $application->mobile) }}"
                                            maxlength="11" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="your@email.com" value="{{ old('email', $application->email) }}"
                                            required>
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
                                            placeholder="Mother's Name"
                                            value="{{ old('mothers_name', $application->mothers_name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Father's Full Name</label>
                                        <input type="text" class="form-control" name="fathers_name"
                                            placeholder="Father's Name"
                                            value="{{ old('fathers_name', $application->fathers_name) }}">
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
                                            placeholder="Number, Street, Purok"
                                            value="{{ old('street_address', $application->street_address) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">District</label>
                                        <input type="text" class="form-control" name="district"
                                            placeholder="District" value="{{ old('district', $application->district) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control" name="zip_code"
                                            placeholder="Zip Code" value="{{ old('zip_code', $application->zip_code) }}">
                                    </div>
                                </div>

                                <!-- Hidden inputs for PSGC codes -->
                                <input type="hidden" name="region_code" id="region_code"
                                    value="{{ old('region_code', $application->region_code) }}">
                                <input type="hidden" name="region_name" id="region_name"
                                    value="{{ old('region_name', $application->region_name) }}">
                                <input type="hidden" name="province_code" id="province_code"
                                    value="{{ old('province_code', $application->province_code) }}">
                                <input type="hidden" name="province_name" id="province_name"
                                    value="{{ old('province_name', $application->province_name) }}">
                                <input type="hidden" name="city_code" id="city_code"
                                    value="{{ old('city_code', $application->city_code) }}">
                                <input type="hidden" name="city_name" id="city_name"
                                    value="{{ old('city_name', $application->city_name) }}">
                                <input type="hidden" name="barangay_code" id="barangay_code"
                                    value="{{ old('barangay_code', $application->barangay_code) }}">
                                <input type="hidden" name="barangay_name" id="barangay_name"
                                    value="{{ old('barangay_name', $application->barangay_name) }}">
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
                                            placeholder="City/Municipality"
                                            value="{{ old('birthplace_city', $application->birthplace_city) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace_province"
                                            placeholder="Province"
                                            value="{{ old('birthplace_province', $application->birthplace_province) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Region <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="birthplace_region"
                                            placeholder="Region"
                                            value="{{ old('birthplace_region', $application->birthplace_region) }}"
                                            required>
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
                                            placeholder="Parent/Guardian Name"
                                            value="{{ old('parent_guardian_name', $application->parent_guardian_name) }}"
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
                                            placeholder="Number, Street"
                                            value="{{ old('parent_guardian_street', $application->parent_guardian_street) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_barangay"
                                            placeholder="Barangay"
                                            value="{{ old('parent_guardian_barangay', $application->parent_guardian_barangay) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_district"
                                            placeholder="District"
                                            value="{{ old('parent_guardian_district', $application->parent_guardian_district) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_city"
                                            placeholder="City/Municipality"
                                            value="{{ old('parent_guardian_city', $application->parent_guardian_city) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_province"
                                            placeholder="Province"
                                            value="{{ old('parent_guardian_province', $application->parent_guardian_province) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="parent_guardian_region"
                                            placeholder="Region"
                                            value="{{ old('parent_guardian_region', $application->parent_guardian_region) }}">
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
                                            <option value="ELEMENTARY GRADUATE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'ELEMENTARY GRADUATE' ? 'selected' : '' }}>
                                                ELEMENTARY GRADUATE</option>
                                            <option value="HIGH SCHOOL GRADUATE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'HIGH SCHOOL GRADUATE' ? 'selected' : '' }}>
                                                HIGH SCHOOL GRADUATE</option>
                                            <option value="TVET GRADUATE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'TVET GRADUATE' ? 'selected' : '' }}>
                                                TVET GRADUATE</option>
                                            <option value="COLLEGE LEVEL"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'COLLEGE LEVEL' ? 'selected' : '' }}>
                                                COLLEGE LEVEL</option>
                                            <option value="COLLEGE GRADUATE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'COLLEGE GRADUATE' ? 'selected' : '' }}>
                                                COLLEGE GRADUATE</option>
                                            <option value="MASTER'S DEGREE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == "MASTER'S DEGREE" ? 'selected' : '' }}>
                                                MASTER'S DEGREE</option>
                                            <option value="DOCTORAL DEGREE"
                                                {{ old('highest_educational_attainment', $application->highest_educational_attainment) == 'DOCTORAL DEGREE' ? 'selected' : '' }}>
                                                DOCTORAL DEGREE</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Employment Status <span
                                                class="text-danger">*</span></label>
                                        <select name="employment_status" class="form-select" required>
                                            <option value="">Select Employment Status</option>
                                            <option value="CASUAL"
                                                {{ old('employment_status', $application->employment_status) == 'CASUAL' ? 'selected' : '' }}>
                                                CASUAL</option>
                                            <option value="JOB ORDER"
                                                {{ old('employment_status', $application->employment_status) == 'JOB ORDER' ? 'selected' : '' }}>
                                                JOB ORDER</option>
                                            <option value="PROBATIONARY"
                                                {{ old('employment_status', $application->employment_status) == 'PROBATIONARY' ? 'selected' : '' }}>
                                                PROBATIONARY</option>
                                            <option value="PERMANENT"
                                                {{ old('employment_status', $application->employment_status) == 'PERMANENT' ? 'selected' : '' }}>
                                                PERMANENT</option>
                                            <option value="SELF-EMPLOYED"
                                                {{ old('employment_status', $application->employment_status) == 'SELF-EMPLOYED' ? 'selected' : '' }}>
                                                SELF-EMPLOYED</option>
                                            <option value="OFW"
                                                {{ old('employment_status', $application->employment_status) == 'OFW' ? 'selected' : '' }}>
                                                OFW</option>
                                            <option value="NONE"
                                                {{ old('employment_status', $application->employment_status) == 'NONE' ? 'selected' : '' }}>
                                                NONE</option>
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
                                <div id="work-experiences">
                                    @if ($application->workExperiences && count($application->workExperiences) > 0)
                                        @foreach ($application->workExperiences as $index => $work)
                                            <div class="border p-3 mb-2 rounded">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][company_name]"
                                                            placeholder="Name of company"
                                                            value="{{ $work['company_name'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][position]"
                                                            placeholder="Position" value="{{ $work['position'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][date_from]"
                                                            placeholder="From" value="{{ $work['date_from'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][date_to]"
                                                            placeholder="To" value="{{ $work['date_to'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][monthly_salary]"
                                                            placeholder="Monthly salary"
                                                            value="{{ $work['monthly_salary'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][appointment_status]"
                                                            placeholder="Status"
                                                            value="{{ $work['appointment_status'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="work_experiences[{{ $index }}][years_experience]"
                                                            placeholder="No. of yrs. working exp"
                                                            value="{{ $work['years_experience'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                                            onclick="this.closest('.border').remove()">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
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
                                <div id="trainings">
                                    @if ($application->trainings && count($application->trainings) > 0)
                                        @foreach ($application->trainings as $index => $training)
                                            <div class="border p-3 mb-2 rounded">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][title]"
                                                            placeholder="Title" value="{{ $training['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][venue]"
                                                            placeholder="Venue" value="{{ $training['venue'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][date_from]"
                                                            placeholder="From"
                                                            value="{{ $training['date_from'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][date_to]"
                                                            placeholder="To" value="{{ $training['date_to'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][hours]"
                                                            placeholder="No. of Hours"
                                                            value="{{ $training['hours'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="trainings[{{ $index }}][conducted_by]"
                                                            placeholder="Conducted by"
                                                            value="{{ $training['conducted_by'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                                            onclick="this.closest('.border').remove()">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
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
                                <div id="licensure-exams">
                                    @if ($application->licensureExams && count($application->licensureExams) > 0)
                                        @foreach ($application->licensureExams as $index => $exam)
                                            <div class="border p-3 mb-2 rounded">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][title]"
                                                            placeholder="Title" value="{{ $exam['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][year_taken]"
                                                            placeholder="Year taken"
                                                            value="{{ $exam['year_taken'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][exam_venue]"
                                                            placeholder="Examination venue"
                                                            value="{{ $exam['exam_venue'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][rating]"
                                                            placeholder="Rating" value="{{ $exam['rating'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][remarks]"
                                                            placeholder="Remarks" value="{{ $exam['remarks'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="licensure_exams[{{ $index }}][expiry_date]"
                                                            placeholder="Expiry date"
                                                            value="{{ $exam['expiry_date'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                                            onclick="this.closest('.border').remove()">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
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
                                <div id="competency-assessments">
                                    @if ($application->competencyAssessments && count($application->competencyAssessments) > 0)
                                        @foreach ($application->competencyAssessments as $index => $assessment)
                                            <div class="border p-3 mb-2 rounded">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][title]"
                                                            placeholder="Title"
                                                            value="{{ $assessment['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][qualification_level]"
                                                            placeholder="Qualification level"
                                                            value="{{ $assessment['qualification_level'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][industry_sector]"
                                                            placeholder="Industry sector"
                                                            value="{{ $assessment['industry_sector'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][certificate_number]"
                                                            placeholder="Certificate number"
                                                            value="{{ $assessment['certificate_number'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][date_of_issuance]"
                                                            placeholder="Date of issuance"
                                                            value="{{ $assessment['date_of_issuance'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="competency_assessments[{{ $index }}][expiration_date]"
                                                            placeholder="Expiration date"
                                                            value="{{ $assessment['expiration_date'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                                            onclick="this.closest('.border').remove()">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
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
                                            <option value="wage-employed"
                                                {{ old('employment_before_training_status', $application->employment_before_training_status) == 'wage-employed' ? 'selected' : '' }}>
                                                Wage-Employed</option>
                                            <option value="underemployed"
                                                {{ old('employment_before_training_status', $application->employment_before_training_status) == 'underemployed' ? 'selected' : '' }}>
                                                Underemployed</option>
                                            <option value="self-employed"
                                                {{ old('employment_before_training_status', $application->employment_before_training_status) == 'self-employed' ? 'selected' : '' }}>
                                                Self-Employed</option>
                                            <option value="unemployed"
                                                {{ old('employment_before_training_status', $application->employment_before_training_status) == 'unemployed' ? 'selected' : '' }}>
                                                Unemployed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="emp_type_wrapper" style="display: none;">
                                        <label class="form-label">Employment Type (if applicable)</label>
                                        <select name="employment_before_training_type" class="form-select">
                                            <option value="">Select Employment Type</option>
                                            <option value="regular"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'regular' ? 'selected' : '' }}>
                                                Regular</option>
                                            <option value="casual"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'casual' ? 'selected' : '' }}>
                                                Casual</option>
                                            <option value="job order"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'job order' ? 'selected' : '' }}>
                                                Job Order</option>
                                            <option value="probationary"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'probationary' ? 'selected' : '' }}>
                                                Probationary</option>
                                            <option value="permanent"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'permanent' ? 'selected' : '' }}>
                                                Permanent</option>
                                            <option value="contractual"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'contractual' ? 'selected' : '' }}>
                                                Contractual</option>
                                            <option value="temporary"
                                                {{ old('employment_before_training_type', $application->employment_before_training_type) == 'temporary' ? 'selected' : '' }}>
                                                Temporary</option>
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
                                            <option value="no grade completed"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'no grade completed' ? 'selected' : '' }}>
                                                No Grade Completed</option>
                                            <option value="elementary undergraduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'elementary undergraduate' ? 'selected' : '' }}>
                                                Elementary Undergraduate</option>
                                            <option value="elementary graduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'elementary graduate' ? 'selected' : '' }}>
                                                Elementary Graduate</option>
                                            <option value="high school undergraduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'high school undergraduate' ? 'selected' : '' }}>
                                                High School Undergraduate</option>
                                            <option value="high school graduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'high school graduate' ? 'selected' : '' }}>
                                                High School Graduate</option>
                                            <option value="junior high (k-12)"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'junior high (k-12)' ? 'selected' : '' }}>
                                                Junior High (K-12)</option>
                                            <option value="senior high (k-12)"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'senior high (k-12)' ? 'selected' : '' }}>
                                                Senior High (K-12)</option>
                                            <option value="post-secondary non-tertiary/technical vocational undergraduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'post-secondary non-tertiary/technical vocational undergraduate' ? 'selected' : '' }}>
                                                Post-Secondary Non-Tertiary/Technical Vocational Undergraduate</option>
                                            <option value="post-secondary non-tertiary/technical vocational graduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'post-secondary non-tertiary/technical vocational graduate' ? 'selected' : '' }}>
                                                Post-Secondary Non-Tertiary/Technical Vocational Graduate</option>
                                            <option value="college undergraduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'college undergraduate' ? 'selected' : '' }}>
                                                College Undergraduate</option>
                                            <option value="college graduate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'college graduate' ? 'selected' : '' }}>
                                                College Graduate</option>
                                            <option value="masteral"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'masteral' ? 'selected' : '' }}>
                                                Masteral</option>
                                            <option value="doctorate"
                                                {{ old('educational_attainment_before_training', $application->educational_attainment_before_training) == 'doctorate' ? 'selected' : '' }}>
                                                Doctorate</option>
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
                                    @php
                                        $learnerClassification = is_array($application->learner_classification)
                                            ? $application->learner_classification
                                            : json_decode($application->learner_classification, true) ?? [];
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="4ps_beneficiary" id="lc1"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('4ps_beneficiary', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc1">4Ps Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="displaced_workers" id="lc2"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('displaced_workers', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc2">Displaced Workers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="afp_pnp_wounded" id="lc3"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('afp_pnp_wounded', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc3">Family Members of AFP/PNP
                                                Wounded-in-Action</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="industry_workers" id="lc4"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('industry_workers', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc4">Industry Workers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="out_of_school_youth"
                                                id="lc5"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('out_of_school_youth', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc5">Out-of-School-Youth</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="rebel_returnees" id="lc6"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('rebel_returnees', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc6">Rebel Returnees/Decommissioned
                                                Combatants</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="tesda_alumni" id="lc7"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('tesda_alumni', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc7">TESDA Alumni</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="disaster_victims" id="lc8"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('disaster_victims', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc8">Victim of Natural Disasters and
                                                Calamities</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="agrarian_reform" id="lc9"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('agrarian_reform', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc9">Agrarian Reform
                                                Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="drug_dependents" id="lc10"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('drug_dependents', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc10">Drug Dependents
                                                Surrenderees</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="farmers_fishermen"
                                                id="lc11"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('farmers_fishermen', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc11">Farmers and Fishermen</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="inmates_detainees"
                                                id="lc12"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('inmates_detainees', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc12">Inmates and Detainees</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="ofw_dependent" id="lc13"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('ofw_dependent', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc13">OFW Dependent</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="returning_ofw" id="lc14"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('returning_ofw', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc14">Returning/Repatriated
                                                OFW</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="tvet_trainers" id="lc15"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('tvet_trainers', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc15">TVET Trainers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="wounded_afp_pnp" id="lc16"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('wounded_afp_pnp', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc16">Wounded-in-Action AFP & PNP
                                                Personnel</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="balik_probinsya" id="lc17"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('balik_probinsya', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc17">Balik Probinsya</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="afp_pnp_killed" id="lc18"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('afp_pnp_killed', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc18">Family Members of AFP/PNP
                                                Killed-in-Action</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="indigenous_people"
                                                id="lc19"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('indigenous_people', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc19">Indigenous People & Cultural
                                                Communities</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="milf_beneficiary"
                                                id="lc20"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('milf_beneficiary', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc20">MILF Beneficiary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="rcef_resp" id="lc21"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('rcef_resp', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc21">RCEF-RESP</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="student" id="lc22"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('student', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc22">Student</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="uniformed_personnel"
                                                id="lc23"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('uniformed_personnel', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc23">Uniformed Personnel</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="learner_classification[]" value="others" id="lc24"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('others', old('learner_classification', $learnerClassification)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lc24">Others</label>
                                        </div>
                                        <div class="mt-2" id="others-input-wrapper"
                                            style="{{ is_array(old('learner_classification', $learnerClassification)) && in_array('others', old('learner_classification', $learnerClassification)) ? 'display: block;' : 'display: none;' }}">
                                            <input type="text" class="form-control"
                                                name="learner_classification_other" id="others_text_input"
                                                placeholder="Please specify"
                                                value="{{ old('learner_classification_other', $application->learner_classification_other) }}"
                                                {{ is_array(old('learner_classification', $learnerClassification)) && in_array('others', old('learner_classification', $learnerClassification)) ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Scholarship Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">If Scholar, What Type of Scholarship Package?</label>
                                        <input type="text" class="form-control" name="scholarship_type"
                                            placeholder="e.g., TWSP, PESFA, STEP, others"
                                            value="{{ old('scholarship_type', $application->scholarship_type) }}">
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
                                        through
                                        its website and thereby giving my consent in the processing of my personal
                                        information
                                        indicated in this Learners Profile. The processing includes scholarships,
                                        employment,
                                        survey, and all other related TESDA programs that may be beneficial to my
                                        qualifications.</small>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privacy_consent"
                                        value="1" id="privacy_consent" required
                                        {{ old('privacy_consent', $application->privacy_consent) ? 'checked' : '' }}>
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
                                Update Application <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this update? Any unsaved changes will be lost.
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
            // ========== APPLICATION TYPE ==========
            const applicationType = "{{ $application->application_type }}";
            const steps = applicationType === 'TWSP' ? ['step-0', 'step-1', 'step-2', 'step-3', 'step-4',
                'step-5'
            ] : ['step-1', 'step-2', 'step-3', 'step-4', 'step-5'];

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

            // ========== STEP NAVIGATION FUNCTIONS ==========
            function showStep(stepNumber) {
                console.log('Showing step:', stepNumber);

                // Hide all steps
                steps.forEach(step => {
                    const element = document.getElementById(step);
                    if (element) element.style.display = 'none';
                });

                // Show current step
                const currentStep = document.getElementById(`step-${stepNumber}`);
                if (currentStep) {
                    currentStep.style.display = 'block';
                }

                // Update progress bar
                updateProgressBar(stepNumber);

                // Scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            function updateProgressBar(stepNumber) {
                const totalSteps = applicationType === 'TWSP' ? 6 : 5;
                // For TWSP, step 0 is actually step 1 in the progress display
                const displayStep = applicationType === 'TWSP' ? stepNumber + 1 : stepNumber;
                const progressPercentage = (displayStep / totalSteps) * 100;
                const progressBar = document.getElementById('progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${progressPercentage}%`;
                    progressBar.setAttribute('aria-valuenow', progressPercentage);
                    progressBar.textContent = `Step ${displayStep} of ${totalSteps}`;
                }
            }

            // Start at appropriate step
            if (applicationType === 'TWSP') {
                showStep(0); // Start at Step 0 (TWSP Documents) for TWSP applications
            } else {
                showStep(1); // Start at Step 1 for Assessment Only
            }

            // ========== NAVIGATION BUTTONS ==========

            // Step 0 Navigation
            document.getElementById('next-from-step-0')?.addEventListener('click', function() {
                showStep(1);
            });

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

            // ========== CANCEL BUTTON HANDLER ==========
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));

            document.getElementById('cancel-step-0')?.addEventListener('click', function() {
                cancelModal.show();
            });

            document.getElementById('confirm-cancel').addEventListener('click', function() {
                window.location.href = '{{ route('applicant.dashboard') }}';
            });

            // ========== VALIDATION FUNCTIONS ==========
            function validateStep1() {
                const programSelect = document.getElementById('title_of_assessment_applied_for');

                if (!programSelect.value) {
                    programSelect.classList.add('is-invalid');
                    programSelect.focus();
                    alert('Please select a program');
                    return false;
                }
                programSelect.classList.remove('is-invalid');
                return true;
            }

            function validateStep2() {
                const requiredFields = document.querySelectorAll('#step-2 [required]');

                const mobileInput = document.querySelector('input[name="mobile"]');
                if (mobileInput && mobileInput.value.length > 0 && mobileInput.value.length !== 11) {
                    mobileInput.classList.add('is-invalid');
                    alert('Mobile number must be exactly 11 digits');
                    mobileInput.focus();
                    return false;
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

                        ageInput.value = age >= 0 ? age : 0;
                    }
                });
            }

            // ========== MOBILE NUMBER VALIDATION ==========
            const mobileInput = document.querySelector('input[name="mobile"]');
            if (mobileInput) {
                mobileInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
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

                // Initial state
                if (empBeforeStatus.value === 'wage-employed' || empBeforeStatus.value === 'underemployed') {
                    empTypeWrapper.style.display = 'block';
                }
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
            const currentPhoto = document.getElementById('current-photo');

            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            photoPreview.src = e.target.result;
                            photoPreview.style.display = 'inline-block';
                            if (currentPhoto) {
                                currentPhoto.style.display = 'none';
                            }
                        };
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        photoPreview.style.display = 'none';
                        if (currentPhoto) {
                            currentPhoto.style.display = 'inline-block';
                        }
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

            // Setup all file previews for TWSP documents
            if (applicationType === 'TWSP') {
                setupFilePreview('psa_birth_certificate', 'preview_psa_birth_certificate');
                setupFilePreview('psa_marriage_contract', 'preview_psa_marriage_contract');
                setupFilePreview('high_school_document', 'preview_high_school_document');
                setupFilePreview('id_pictures_1x1', 'preview_id_pictures_1x1');
                setupFilePreview('id_pictures_passport', 'preview_id_pictures_passport');
                setupFilePreview('government_school_id', 'preview_government_school_id');
                setupFilePreview('certificate_of_indigency', 'preview_certificate_of_indigency');
            }

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
                    setTimeout(restoreAddressValues, 500);
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

            // Restore address values
            function restoreAddressValues() {
                const oldRegionCode = "{{ old('region_code', $application->region_code) }}";
                const oldProvinceCode = "{{ old('province_code', $application->province_code) }}";
                const oldCityCode = "{{ old('city_code', $application->city_code) }}";
                const oldBarangayCode = "{{ old('barangay_code', $application->barangay_code) }}";

                if (oldRegionCode && regionSelect) {
                    regionSelect.value = oldRegionCode;
                    const regionOpt = regionSelect.selectedOptions[0];
                    if (regionOpt) {
                        regionCode.value = regionOpt.value;
                        regionName.value = regionOpt.dataset.name;

                        if (oldProvinceCode) {
                            loadProvinces(oldRegionCode).then(() => {
                                setTimeout(() => {
                                    provinceSelect.value = oldProvinceCode;
                                    const provinceOpt = provinceSelect.selectedOptions[0];
                                    if (provinceOpt) {
                                        provinceCode.value = provinceOpt.value;
                                        provinceName.value = provinceOpt.dataset.name;

                                        if (oldCityCode) {
                                            loadCities(oldProvinceCode).then(() => {
                                                setTimeout(() => {
                                                    citySelect.value = oldCityCode;
                                                    const cityOpt = citySelect
                                                        .selectedOptions[0];
                                                    if (cityOpt) {
                                                        cityCode.value = cityOpt
                                                            .value;
                                                        cityName.value = cityOpt
                                                            .dataset.name;

                                                        if (oldBarangayCode) {
                                                            loadBarangays(
                                                                    oldCityCode)
                                                                .then(() => {
                                                                    setTimeout(
                                                                        () => {
                                                                            barangaySelect
                                                                                .value =
                                                                                oldBarangayCode;
                                                                            const
                                                                                barangayOpt =
                                                                                barangaySelect
                                                                                .selectedOptions[
                                                                                    0
                                                                                ];
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
                                                                        },
                                                                        300);
                                                                });
                                                        }
                                                    }
                                                }, 300);
                                            });
                                        }
                                    }
                                }, 300);
                            });
                        }
                    }
                }
            }

            // Initialize regions
            loadRegions();

            // ========== DYNAMIC SECTION FUNCTIONS ==========

            // Work Experience
            function addWorkExperienceRow() {
                const wrap = document.getElementById('work-experiences');
                const idx = wrap.children.length;
                const div = document.createElement('div');
                div.className = 'border p-3 mb-2 rounded';
                div.innerHTML = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][company_name]" placeholder="Name of company">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][position]" placeholder="Position">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control form-control-sm" name="work_experiences[${idx}][date_from]" placeholder="From">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control form-control-sm" name="work_experiences[${idx}][date_to]" placeholder="To">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="work_experiences[${idx}][monthly_salary]" placeholder="Monthly salary">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="work_experiences[${idx}][appointment_status]" placeholder="Status">
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control form-control-sm" name="work_experiences[${idx}][years_experience]" placeholder="No. of yrs. working exp">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                Remove
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
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][title]" placeholder="Title">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][venue]" placeholder="Venue">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control form-control-sm" name="trainings[${idx}][date_from]" placeholder="From">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control form-control-sm" name="trainings[${idx}][date_to]" placeholder="To">
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control form-control-sm" name="trainings[${idx}][hours]" placeholder="No. of Hours">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="trainings[${idx}][conducted_by]" placeholder="Conducted by">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                Remove
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
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][title]" placeholder="Title">
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control form-control-sm" name="licensure_exams[${idx}][year_taken]" placeholder="Year taken">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][exam_venue]" placeholder="Examination venue">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][rating]" placeholder="Rating">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="licensure_exams[${idx}][remarks]" placeholder="Remarks">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control form-control-sm" name="licensure_exams[${idx}][expiry_date]" placeholder="Expiry date">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                Remove
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
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][title]" placeholder="Title">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][qualification_level]" placeholder="Qualification level">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][industry_sector]" placeholder="Industry sector">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="competency_assessments[${idx}][certificate_number]" placeholder="Certificate number">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control form-control-sm" name="competency_assessments[${idx}][date_of_issuance]" placeholder="Date of issuance">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control form-control-sm" name="competency_assessments[${idx}][expiration_date]" placeholder="Expiration date">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.border').remove()">
                                Remove
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
