<!-- Existing Schedules Modal -->
<div class="modal fade" id="existingSchedulesModal{{ $loop->index }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Existing Schedules - {{ $program }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                @php
                    $programSchedules = \App\Models\TrainingSchedule::where('nc_program', $program)->get();
                    $regularSchedules = $programSchedules->where('schedule_type', 'regular');
                    $weekendSchedules = $programSchedules->where('schedule_type', 'weekend');
                @endphp

                {{-- REGULAR SCHEDULES --}}
                <h6 class="fw-bold text-success mb-3">Regular Schedules</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Schedule Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Days</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($regularSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->schedule_name }}</td>
                                    <td>{{ $schedule->start_date->format('M d') }} - {{ $schedule->end_date->format('M d, Y') }}</td>
                                    <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                    <td>{{ $schedule->days }}</td>
                                    <td><span class="badge bg-info">{{ $schedule->enrolledApplicants->count() }}/{{ $schedule->max_students }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $schedule->status == 'active' ? 'success' : ($schedule->status == 'completed' ? 'secondary' : 'danger') }}">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editSchedule({{ $schedule->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.schedules.delete', $schedule) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No regular schedules found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- WEEKEND SCHEDULES --}}
                <h6 class="fw-bold text-warning mb-3">Weekend Schedules</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Schedule Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Days</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weekendSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->schedule_name }}</td>
                                    <td>{{ $schedule->start_date->format('M d') }} - {{ $schedule->end_date->format('M d, Y') }}</td>
                                    <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                    <td>{{ $schedule->days }}</td>
                                    <td><span class="badge bg-info">{{ $schedule->enrolledApplicants->count() }}/{{ $schedule->max_students }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $schedule->status == 'active' ? 'success' : ($schedule->status == 'completed' ? 'secondary' : 'danger') }}">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editSchedule({{ $schedule->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.schedules.delete', $schedule) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No weekend schedules found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
