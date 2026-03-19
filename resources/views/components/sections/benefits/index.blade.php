<section class="py-5 text-white pt-5" id="benefits">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-white mt-3">Benefits of TESDA Training</h2>
      <p class="lead text-white">
        Discover the advantages of getting certified with TESDA and how it can transform your career prospects.
      </p>
    </div>

    @php
      $benefits = [
        [
          'icon' => 'bi-award',
          'title' => 'National Certification',
          'desc' => 'Earn NC I, NC II, NC III, or NC IV certifications recognized nationwide and abroad.'
        ],
        [
          'icon' => 'bi-briefcase',
          'title' => 'Job Placement',
          'desc' => 'Access employment opportunities through our network of partner companies and agencies.'
        ],
        [
          'icon' => 'bi-globe',
          'title' => 'Global Opportunities',
          'desc' => 'TESDA certifications are recognized internationally, opening doors for overseas employment.'
        ],
        [
          'icon' => 'bi-clock',
          'title' => 'Flexible Training',
          'desc' => 'Choose from full-time, part-time, or modular training schedules that fit your lifestyle.'
        ],
        [
          'icon' => 'bi-people',
          'title' => 'Expert Trainers',
          'desc' => 'Learn from industry professionals and TESDA-accredited assessors with real-world experience.'
        ],
        [
          'icon' => 'bi-star',
          'title' => 'Affordable Education',
          'desc' => 'Many programs are offered free or with scholarships through various government initiatives.'
        ],
      ];
    @endphp

    <div class="row g-4">
      @foreach($benefits as $benefit)
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 border-0 rounded-3 shadow-sm text-dark" style="background-color: rgba(255,255,255,0.1);">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bg-warning p-1 px-2 {{ $benefit['icon'] }} fs-2 text-light"></i>
              </div>
              <h5 class="card-title fw-bold text-light">{{ $benefit['title'] }}</h5>
              <p class="card-text text-light">{{ $benefit['desc'] }}</p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
