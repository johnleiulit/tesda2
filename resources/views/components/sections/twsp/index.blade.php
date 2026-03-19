@if($twspAnnouncement && $twspAnnouncement->hasAvailableSlots())
<section class="twsp-announcement py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="alert alert-success">
                    <h3>🎓 SHC - TVET Training and Assessment Center</h3>
                    <h4>Now Offering: {{ $twspAnnouncement->program_name }}</h4>
                    <p class="mb-0">
                        <strong>Available Slots:</strong> 
                        {{ $twspAnnouncement->getRemainingSlots() }} / {{ $twspAnnouncement->total_slots }}
                    </p>
                    <a href="{{ route('applicant.apply') }}" class="btn btn-primary btn-lg mt-3">
                        Apply for TWSP Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
