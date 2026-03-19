@extends('layouts.admin')

@section('title', 'Admin Dashboard - TESDA')
@section('page-title', 'Dashboard Analytics')

@section('content')
    {{-- Overview Cards --}}
    @include('admin.dashboard.overview.cards')

    {{-- Analytics Sections --}}
    <div class="row">
        <div class="col-12 mb-4">
            @include('admin.dashboard.applicant.index')
        </div>
    </div>

    {{-- Training Analytics Section (Full Width) --}}
    <div class="row">
        <div class="col-12 mb-4">
            @include('admin.dashboard.training.index')
        </div>
    </div>

    {{-- Assessment Analytics Section (Full Width) --}}
    <div class="row">
        <div class="col-12 mb-4">
            @include('admin.dashboard.assessment.index')
        </div>
    </div>

    {{-- Employment Feedback Section (Full Width) --}}
    <div class="row">
        <div class="col-12">
            @include('admin.dashboard.employment.index')
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .analytics-card {
            height: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .analytics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
@endpush
