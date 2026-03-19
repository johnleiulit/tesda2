<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="metric-value">{{ $overview['total_applications'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-file-earmark-text me-1"></i>Total Applications
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="metric-value">{{ $overview['pending_applications'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-clock me-1"></i>Pending Applications
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="metric-value">{{ $overview['active_training_batches'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-mortarboard me-1"></i>Active Training Batches
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="metric-value">{{ $overview['competency_rate'] ?? 0 }}%</div>
            <div class="metric-label">
                <i class="bi bi-trophy me-1"></i>Overall Competency Rate
            </div>
        </div>
    </div>
</div>
