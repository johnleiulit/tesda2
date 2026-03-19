<div class="modal fade" id="scheduleModal{{ $loop->index }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Schedules - {{ $program }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Create New Schedule Form -->
                <div class="mb-4">
                    <h6>Create New Schedule</h6>
                    <form method="POST" action="{{ route('admin.schedules.store') }}">
                        @csrf
                        <input type="hidden" name="nc_program" value="{{ $program }}">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Schedule Type</label>
                                <select name="schedule_type" class="form-select" required>
                                    <option value="">Select Schedule Type</option>
                                    <option value="regular">Regular Schedule</option>
                                    <option value="weekend">Weekend Schedule</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Schedule Name</label>
                                <input type="text" name="schedule_name" class="form-control" placeholder="e.g., Batch 1, Weekend Class" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instructor</label>
                                <input type="text" name="instructor" class="form-control" placeholder="Instructor Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Days</label>
                                <select name="days" class="form-select" required>
                                    <option value="">Select Days</option>
                                    <option value="Monday-Friday">Monday-Friday</option>
                                    <option value="Saturday-Sunday">Saturday-Sunday</option>
                                    <option value="Monday-Wednesday-Friday">Monday-Wednesday-Friday</option>
                                    <option value="Tuesday-Thursday">Tuesday-Thursday</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Students</label>
                                <input type="number" name="max_students" class="form-control" min="1" max="100" value="25" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control" placeholder="Training Venue" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Additional details..." required></textarea>
                            </div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Create Schedule
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Schedules - Separated by Type -->
                <div class="mt-4">
                    <h6>Existing Schedules</h6>
                    
                    <!-- Regular Schedules -->
                    <div class="mb-3">
                        <h6 class="text-primary">Regular Schedules</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-primary">
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
                                    @php
                                        $regularSchedules = \App\Models\TrainingSchedule::where('nc_program', $program)
                                            ->where('schedule_type', 'regular')->get();
                                    @endphp
                                    
                                    @forelse($regularSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->schedule_name }}</td>
                                            <td>{{ $schedule->start_date->format('M d') }} - {{ $schedule->end_date->format('M d, Y') }}</td>
                                            <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                            <td>{{ $schedule->days }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $schedule->enrolledApplicants->count() }}/{{ $schedule->max_students }}</span>
                                            </td>
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
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No regular schedules created yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Weekend Schedules -->
                    <div class="mb-3">
                        <h6 class="text-success">Weekend Schedules</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-success">
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
                                    @php
                                        $weekendSchedules = \App\Models\TrainingSchedule::where('nc_program', $program)
                                            ->where('schedule_type', 'weekend')->get();
                                    @endphp
                                    
                                    @forelse($weekendSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->schedule_name }}</td>
                                            <td>{{ $schedule->start_date->format('M d') }} - {{ $schedule->end_date->format('M d, Y') }}</td>
                                            <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                            <td>{{ $schedule->days }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $schedule->enrolledApplicants->count() }}/{{ $schedule->max_students }}</span>
                                            </td>
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
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No weekend schedules created yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>