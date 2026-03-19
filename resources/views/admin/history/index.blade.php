@extends('layouts.admin')

@section('title', 'Application History - TESDA')
@section('page-title', 'Application History')

@section('content')
    <div class="card">
        <div class="card-header bg-light">
            <input type="text" id="searchInput" value="{{ request('q') }}" class="form-control"
                placeholder="Search applicant or program...">
        </div>
        <div id="applicationsBlock">
            <div id="applicationsTable" class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Title of Assessment</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th style="width:180px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apps as $app)
                            <tr>
                                <td>{{ $app->user?->name ?? '—' }}</td>
                                <td>{{ $app->title_of_assessment_applied_for }}</td>
                                <td>
                                    @php $map=['pending'=>'secondary','approved'=>'success','rejected'=>'danger']; @endphp
                                    <span
                                        class="badge bg-{{ $map[$app->status] ?? 'secondary' }}">{{ ucfirst($app->status) }}</span>
                                </td>
                                <td>{{ $app->created_at?->toDayDateTimeString() }}</td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $app) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                    @if ($app->status === \App\Models\Application::STATUS_PENDING)
                                        <form method="POST" action="{{ route('admin.applications.approve', $app) }}"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this application?')">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.applications.reject', $app) }}"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Reject this application?')">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3" id="applicationsPagination">
            {{ $apps->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        let timer;

        // Debounced live search
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                let query = this.value;

                fetch(`{{ route('admin.history.index') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        document.getElementById('applicationsBlock').innerHTML =
                            doc.querySelector('#applicationsBlock').innerHTML;
                    })
                .catch(error => console.error('Error fetching search results:', error));
            }, 400);
        });
    </script>
@endsection
