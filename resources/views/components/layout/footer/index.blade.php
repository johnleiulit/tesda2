<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- About Section -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">About TESDA</h5>
                <p class="small text-light">
                    Empowering Filipinos with world-class technical education 
                    and skills development for global competitiveness.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#about" class="text-light text-decoration-none small">About</a></li>
                    <li><a href="#programs" class="text-light text-decoration-none small">Programs</a></li>
                    <li><a href="{{ route('benefits') }}" class="text-light text-decoration-none small">Benefits</a></li>
                    <li><a href="#howtoenroll" class="text-light text-decoration-none small">How to Enroll</a></li>
                    <li><a href="#contact" class="text-light text-decoration-none small">Contact</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Contact Us</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill text-warning"></i>
                        1 Merchan Street, Lucena City. OLMM Building. 2<sup>nd</sup> Floor, Room 201
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone-fill text-warning"></i>
                        (042) 7103888 / 0991-1194131
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill text-warning"></i>
                        shctesda@shc.edu.ph
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-facebook text-warning"></i>
                        SHC-TVET Training and Assessment Center
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock-fill text-warning"></i>
                        Mon-Fri: 8:00 AM - 5:00 PM
                    </li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Follow Us</h5>
                <div class="d-flex gap-3 mb-3">
                    <a href="#" class="text-warning fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-warning fs-4"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-warning fs-4"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="text-warning fs-4"><i class="bi bi-instagram"></i></a>
                </div>
                <p class="small text-light mb-0">
                    <a href="https://www.tesda.gov.ph" target="_blank" class="text-warning text-decoration-none">
                        Official TESDA Website
                    </a>
                </p>
            </div>
        </div>

        <!-- Bottom Bar -->
        <hr class="my-4 border-light">
        <div class="row">
            <div class="col-md-6">
                <p class="small text-light mb-0">
                    © {{ date('Y') }} TESDA. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-light text-decoration-none small me-3">Privacy Policy</a>
                <a href="#" class="text-light text-decoration-none small">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
<style>