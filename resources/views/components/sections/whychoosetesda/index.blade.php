<section class="py-5 text-white">
  <div class="container">

    <!-- Section Header -->
    <div class="text-center mb-5">
      <h2 class="fw-bold">Why Choose Our TESDA Assessment Center?</h2>
      <p class="lead">
        We provide a reliable, accessible, and professional assessment experience to help you earn your National Certification.
      </p>
    </div>

    <div class="row g-4">

      @php
        $features = [
          // [
          //   'icon' => 'bi-graph-up',
          //   'title' => 'Industry-Driven Curriculum',
          //   'desc' => 'Courses are designed based on current labor market demands to ensure job relevance.'
          // ],
          [
            'icon' => 'bi-tools',
            'title' => 'Hands-On Training',
            'desc' => 'Practical learning approach using real equipment and real-world scenarios.'
          ],
          // [
          //   'icon' => 'bi-shield-check',
          //   'title' => 'Government-Backed Institution',
          //   'desc' => 'TESDA is a trusted government agency ensuring quality and standardized training.'
          // ],
          // [
          //   'icon' => 'bi-briefcase',
          //   'title' => 'High Employment Rate',
          //   'desc' => 'Many graduates find jobs locally and abroad after certification.'
          // ],
          [
            'icon' => 'bi-award-fill',
            'title' => 'Accredited Assessors',
            'desc' => 'Qualified and experienced TESDA-certified assessors.'
          ],
           [
            'icon' => 'bi-clock-fill',
            'title' => 'Fast Processing',
            'desc' => 'Efficient application review and status updates.'
          ],
           [
            'icon' => 'bi-laptop-fill',
            'title' => 'Online Tracking',
            'desc' => 'Monitor your application anytime through your dashboard'
          ],
        ];
      @endphp

      @foreach ($features as $feature)
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm text-center p-4" id="card">
            <div class="mb-3">
              <i class="bi {{ $feature['icon'] }} fs-1 text-warning"></i>
            </div>
            <h5 class="fw-bold text-white">
              {{ $feature['title'] }}
            </h5>
            <p class="text-light small">
              {{ $feature['desc'] }}
            </p>
          </div>
        </div>
      @endforeach

    </div>
  </div>
</section>
