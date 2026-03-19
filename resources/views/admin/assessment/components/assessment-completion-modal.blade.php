{{-- ============================= --}}
{{-- COC PROGRAMS DATA (ONE FILE) --}}
{{-- ============================= --}}
@php
    $cocPrograms = [
        'TOURISM PROMOTION SERVICES NC II' => [
            ['code' => 'COC 1', 'title' => 'Provide Information on Tourism Products and Services'],
            ['code' => 'COC 2', 'title' => 'Promote Tourism Products and Services'],
        ],
        'VISUAL GRAPHIC DESIGN NC III' => [
            ['code' => 'COC 1', 'title' => 'Develop designs for logo and print media'],
            ['code' => 'COC 2', 'title' => 'Develop designs for user experience and user interface'],
            ['code' => 'COC 3', 'title' => 'Develop designs for product packaging'],
            ['code' => 'COC 4', 'title' => 'Design booth and product window/display'],
        ],
        'EVENTS MANAGEMENT SERVICES NC III' => [
            ['code' => 'COC 1', 'title' => 'Pre-Event Planning Services'],
            ['code' => 'COC 2', 'title' => 'Online and/or On-site Events Management Services'],
        ],
        'BOOKKEEPING NC III' => [
            ['code' => 'COC 1', 'title' => 'Journalize Transactions'],
            ['code' => 'COC 2', 'title' => 'Post Journal Entries and Prepare Trial Balance'],
        ],
        'PHARMACY SERVICES NC III' => [
            ['code' => 'COC 1', 'title' => 'Assist in Dispensing Medicines'],
            ['code' => 'COC 2', 'title' => 'Perform Pharmaceutical Calculations'],
            ['code' => 'COC 3', 'title' => 'Perform Inventory Management in Pharmacy'],
        ],
    ];
@endphp


{{-- ============================= --}}
{{-- MODALS PER APPLICANT --}}
{{-- ============================= --}}
@foreach ($assessment_batch->applications as $applicant)
    {{-- ================= PASS MODAL ================= --}}
    <div class="modal fade" id="completeAssessmentModal{{ $applicant->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.assessment-batches.mark-completed', [$assessment_batch, $applicant]) }}">
                    @csrf
                    <input type="hidden" name="result" value="Competent">

                    <div class="modal-header">
                        <h5 class="modal-title">Mark as Completed</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Confirm completion for
                            <strong>{{ $applicant->firstname }} {{ $applicant->surname }}</strong>?
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Remarks (optional)</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Completion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ================= FAIL / NYC MODAL ================= --}}
    @php
        $programKey = strtoupper(trim($applicant->title_of_assessment_applied_for));
        $cocs = $cocPrograms[$programKey] ?? [];
    @endphp

    <div class="modal fade" id="failAssessmentModal{{ $applicant->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.assessment-batches.mark-completed', [$assessment_batch, $applicant]) }}">
                    @csrf
                    <input type="hidden" name="result" value="Not Yet Competent">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Assessment Result - Not Yet Competent</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="alert alert-info">
                            <strong>Applicant:</strong> {{ $applicant->firstname }} {{ $applicant->surname }} <br>
                            <strong>Program:</strong> {{ $applicant->title_of_assessment_applied_for }}
                        </div>

                        <h6>Select COC Results:</h6>

                        @if (count($cocs) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="120">COC Code</th>
                                            <th>COC Title</th>
                                            <th width="200" class="text-center">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cocs as $index => $coc)
                                            <tr>
                                                <td><strong>{{ $coc['code'] }}</strong></td>
                                                <td>{{ $coc['title'] }}</td>
                                                <td class="text-center">
                                                    {{-- Buttons (shown initially) --}}
                                                    <div id="btns-{{ $applicant->id }}-{{ $index }}">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success me-1"
                                                            onclick="selectCOC({{ $applicant->id }}, {{ $index }}, 'competent', '{{ $coc['code'] }}')">
                                                            <i class="bi bi-check-circle"></i> Competent
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="selectCOC({{ $applicant->id }}, {{ $index }}, 'not_yet_competent', '{{ $coc['code'] }}')">
                                                            <i class="bi bi-x-circle"></i> NYC
                                                        </button>
                                                    </div>

                                                    {{-- Badge (hidden initially) --}}
                                                    <div id="badge-{{ $applicant->id }}-{{ $index }}"
                                                        class="d-none">
                                                        <span class="badge"
                                                            id="badge-text-{{ $applicant->id }}-{{ $index }}"></span>
                                                    </div>

                                                    {{-- Hidden inputs --}}
                                                    <input type="hidden" name="coc_results[{{ $coc['code'] }}][code]"
                                                        value="{{ $coc['code'] }}">
                                                    <input type="hidden"
                                                        name="coc_results[{{ $coc['code'] }}][title]"
                                                        value="{{ $coc['title'] }}">
                                                    <input type="hidden"
                                                        id="result-{{ $applicant->id }}-{{ $index }}"
                                                        name="coc_results[{{ $coc['code'] }}][result]" value=""
                                                        required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                No COCs defined for: <strong>{{ $programKey }}</strong>
                            </div>
                        @endif
                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Overall Score (Optional)</label>
                            <input type="number" name="score" class="form-control" min="0" max="100"
                                step="0.01">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        @if (count($cocs) > 0)
                            <button type="submit" class="btn btn-danger">
                                Save Assessment Result
                            </button>
                        @else
                            <button type="button" class="btn btn-danger" disabled>
                                Cannot Save
                            </button>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>
@endforeach
<script>
function selectCOC(applicantId, index, result, cocCode) {
    // Confirm selection
    const resultText = result === 'competent' ? 'COMPETENT' : 'NOT YET COMPETENT';
    const confirmed = confirm(`Mark ${cocCode} as ${resultText}?`);
    
    if (!confirmed) return;
    
    // Hide buttons
    document.getElementById(`btns-${applicantId}-${index}`).classList.add('d-none');
    
    // Show badge
    const badgeDiv = document.getElementById(`badge-${applicantId}-${index}`);
    const badgeText = document.getElementById(`badge-text-${applicantId}-${index}`);
    
    if (result === 'competent') {
        badgeText.className = 'badge bg-success';
        badgeText.textContent = 'COMPETENT';
    } else {
        badgeText.className = 'badge bg-danger';
        badgeText.textContent = 'NOT YET COMPETENT';
    }
    
    badgeDiv.classList.remove('d-none');
    
    // Set hidden input
    document.getElementById(`result-${applicantId}-${index}`).value = result;
}
</script>
