@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.history.training') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Training History
                </a>
            </div>
        </div>

        <!-- Batch Head