<!-- Complete Training Modal -->
@foreach($applicants as $applicant)
    @if($applicant->training_status === 'enrolled')
        <div class="modal fade" id="completeTrainingModal{{ $applicant->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Training</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.applications.complete-training', $applicant) }}">
                        @csrf
                        <div class="modal-body">
                            <p>Mark training as completed for <strong>{{ $applicant->surname }}, {{ $applicant->firstname }}</strong>?</p>
                            <div class="mb-3">
                                <label class="form-label">Training Remarks (Optional)</label>
                                <textarea name="training_remarks" class="form-control" rows="3" placeholder="Training completion remarks..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Complete Training</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Fail Training Modal -->
        <div class="modal fade" id="failTrainingModal{{ $applicant->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Fail Training</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.applications.fail-training', $applicant) }}">
                        @csrf
                        <div class="modal-body">
                            <p>Mark training as failed for <strong>{{ $applicant->surname }}, {{ $applicant->firstname }}</strong>?</p>
                            <div class="mb-3">
                                <label class="form-label">Failure Reason (Required)</label>
                                <textarea name="training_remarks" class="form-control" rows="3" placeholder="Reason for training failure..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Fail Training</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach