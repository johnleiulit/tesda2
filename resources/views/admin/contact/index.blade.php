@extends('layouts.admin')

@section('title', 'Contact Messages - TESDA')
@section('page-title', 'Contact Messages Management')

@section('content')
    <div class="card shadow">
        <div class="card-header py-3 bg-primary">
            <h5 class="m-0 font-weight-bold text-white text-center">Messages from contact form</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr class="{{ $message->is_read ? '' : 'table-warning' }}">
                                <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->message }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if (!$message->is_read)
                                            <form action="{{ route('admin.contact.mark-read', $message->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check"></i> Mark as Read
                                                </button>
                                            </form>
                                        @else
                                            <div>
                                                <span class="badge bg-success">Read</span>
                                            </div>

                                            <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
@endsection
