<section class="contact-hero d-flex align-items-center py-5" id="hero-contact">
    <div class="container mt-3">
        <div class="row align-items-center">

            <!-- LEFT CONTENT -->
            <div class="col-lg-6 text-white">
                <h1 class="display-4 fw-bold mb-4">
                    We’d Love to Hear From You
                </h1>
                <p class="lead mb-3">
                    Have questions about our training programs, assessments, or enrollment process?
                </p>
                <p class="mb-4">
                    SHC – TVET Training and Assessment Center is here to assist you.
                    Contact us today and we’ll be happy to guide you.
                </p>
            </div>

            <!-- RIGHT IMAGE -->
            <div class="col-lg-6 text-center mt-2 mt-lg-0">
                <img src="{{ asset('images/contact/hero.png') }}" alt="TVET Training"
                    class="img-fluid rounded-4 shadow-lg">
            </div>

        </div>
    </div>
</section>
{{-- Get in touch: form + map --}}
<section class="contact-form-map py-5" id="getintouch">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            {{-- Left column: form --}}
            <div class="col-lg-6">
                <div class="contact-form-card h-100 p-4 p-lg-5 rounded-3 shadow-lg">
                    <h2 class="contact-heading fw-bold mb-3">Get in touch with us</h2>
                    <p class="text-muted mb-2">
                        Do you have a question or do you want to learn more about what we do? We're here to help you.
                    </p>
                    <form action="{{ route('contact.submit') }}" method="post" class="contact-form">
                        @csrf
                        <div class="mb-2">
                            <label for="contact-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact-name" name="name" required>
                        </div>
                        <div class="mb-2">
                            <label for="contact-email" class="form-label">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="contact-email" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label for="contact-message" class="form-label">Message <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="contact-message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </form>
                </div>
            </div>
            {{-- Right column: map --}}
            <div class="col-lg-6">
                <div class="contact-map-wrapper h-100 rounded-3 overflow-hidden shadow-lg">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3252.133999844902!2d121.61314537417125!3d13.940234392904804!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd4b583dfc7ddf%3A0x396c171a8c55ebe4!2sSacred%20Heart%20College%20(Lucena)!5e1!3m2!1sen!2sph!4v1770259971243!5m2!1sen!2sph"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" class="m-1 mt-3"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p class="small text-muted mt-4 text-center">
                        <strong>Visit us:</strong> 1 Merchan Street, Lucena City. OLMM Building, 2nd Floor, Room 201.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
