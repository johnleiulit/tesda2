{{-- resources/views/applicant/application-show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('applicant.dashboard') }}" class="btn btn-link p-0 mb-3">← Back to Dashboard</a>
    <h2 class="mb-3">Application Details</h2>

    <div class="card mb-4">
        <div class="card-header">Application Summary</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 280px">Title of Assessment</th>
                                <td>{{ $application->title_ofAssessment_applied_for ?? $application->title_of_assessment_applied_for }}</td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td>
                                    {{ $application->surname }},
                                    {{ $application->firstname }}
                                    @if($application->middlename) {{ $application->middlename }} @endif
                                    @if($application->name_extension) {{ $application->name_extension }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>
                                    {{ $application->street_address }}
                                    @if($application->barangay_name) , {{ $application->barangay_name }} @endif
                                    @if($application->city_name) , {{ $application->city_name }} @endif
                                    @if($application->province_name) , {{ $application->province_name }} @endif
                                    @if($application->region_name) , {{ $application->region_name }} @endif
                                    @if($application->zip_code) , {{ $application->zip_code }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Contact</th>
                                <td>
                                    @if($application->mobile) Mobile: {{ $application->mobile }} @endif
                                    @if($application->email) | Email: {{ $application->email }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Personal</th>
                                <td>
                                    @if($application->sex) Sex: {{ ucfirst($application->sex) }} @endif
                                    @if($application->civil_status) | Civil Status: {{ $application->civil_status }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Birth</th>
                                <td>
                                    @if($application->birthdate) Birthdate: {{ \Illuminate\Support\Carbon::parse($application->birthdate)->toFormattedDateString() }} @endif
                                    @if($application->birthplace) | Birthplace: {{ $application->birthplace }} @endif
                                    @if(!is_null($application->age)) | Age: {{ $application->age }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Education & Employment</th>
                                <td>
                                    @if($application->highest_educational_attainment) HEA: {{ $application->highest_educational_attainment }} @endif
                                    @if($application->employment_status) | Employment Status: {{ $application->employment_status }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Parents</th>
                                <td>
                                    @if($application->mothers_name) Mother: {{ $application->mothers_name }} @endif
                                    @if($application->fathers_name) | Father: {{ $application->fathers_name }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Sections Summary</th>
                                <td>
                                    Work Experiences: {{ $application->workExperiences->count() }},
                                    Trainings: {{ $application->trainings->count() }},
                                    Licensure Exams: {{ $application->licensureExams->count() }},
                                    Competency Assessments: {{ $application->competencyAssessments->count() }}
                                </td>
                            </tr>
                            <tr>
                                <th>Submitted</th>
                                <td>{{ $application->created_at?->toDayDateTimeString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>

            <div class="card mb-3">
                <div class="card-header">Work Experience</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Position</th>
                                    <th>Inclusive Dates</th>
                                    <th>Monthly Salary</th>
                                    <th>Status</th>
                                    <th>Years</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->workExperiences as $exp)
                                    <tr>
                                        <td>{{ $exp->company_name }}</td>
                                        <td>{{ $exp->position }}</td>
                                        <td>
                                            {{ $exp->date_from }} - {{ $exp->date_to }}
                                        </td>
                                        <td>{{ $exp->monthly_salary }}</td>
                                        <td>{{ $exp->appointment_status }}</td>
                                        <td>{{ $exp->years_experience }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Other Training Seminars Attended</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Venue</th>
                                    <th>Inclusive Dates</th>
                                    <th>No. of Hours</th>
                                    <th>Conducted By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->trainings as $t)
                                    <tr>
                                        <td>{{ $t->title }}</td>
                                        <td>{{ $t->venue }}</td>
                                        <td>{{ $t->date_from }} - {{ $t->date_to }}</td>
                                        <td>{{ $t->hours }}</td>
                                        <td>{{ $t->conducted_by }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Licensure Examination(s) Passed</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Year</th>
                                    <th>Venue</th>
                                    <th>Rating</th>
                                    <th>Remarks</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->licensureExams as $e)
                                    <tr>
                                        <td>{{ $e->title }}</td>
                                        <td>{{ $e->year_taken }}</td>
                                        <td>{{ $e->exam_venue }}</td>
                                        <td>{{ $e->rating }}</td>
                                        <td>{{ $e->remarks }}</td>
                                        <td>{{ $e->expiry_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Competency Assessment(s) Passed</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Qualification Level</th>
                                    <th>Industry Sector</th>
                                    <th>Certificate No.</th>
                                    <th>Date of Issuance</th>
                                    <th>Expiration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->competencyAssessments as $c)
                                    <tr>
                                        <td>{{ $c->title }}</td>
                                        <td>{{ $c->qualification_level }}</td>
                                        <td>{{ $c->industry_sector }}</td>
                                        <td>{{ $c->certificate_number }}</td>
                                        <td>{{ $c->date_of_issuance }}</td>
                                        <td>{{ $c->expiration_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

</div>
@endsection