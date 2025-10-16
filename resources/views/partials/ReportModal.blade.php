<!-- resources/views/components/report-modal.blade.php -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0 rounded-4">
      
      {{-- === Header === --}}
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold" id="reportModalLabel">
          <i class="fas fa-flag me-2"></i> Report an Issue
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- === Form === --}}
      <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-body bg-light">

          <!-- Hidden dynamic target -->
          <input type="hidden" name="target_id" id="reportTargetId">
          <input type="hidden" name="target_type" id="reportTargetType">

          {{-- === Section: User Info === --}}
          <div class="alert alert-info small shadow-sm d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
              Please provide as many details as possible. Our team will review your report and take necessary actions.
            </div>
          </div>

          {{-- === Category Field === --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Report Category <span class="text-danger">*</span></label>
            <select name="category" class="form-select" required>
              <option value="" selected disabled>Select a category</option>
              <option value="Bug">🐞 Bug or Technical Issue</option>
              <option value="Scam">🚨 Scam / Fraud</option>
              <option value="Abuse">⚠️ Abuse / Harassment</option>
              <option value="Feedback">💬 Feedback / Suggestion</option>
              <option value="Other">📝 Other</option>
            </select>
          </div>

          {{-- === Severity Field === --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Severity Level</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="severity" id="low" value="Low" checked>
                <label class="form-check-label" for="low">Low</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="severity" id="medium" value="Medium">
                <label class="form-check-label" for="medium">Medium</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="severity" id="high" value="High">
                <label class="form-check-label" for="high">High</label>
              </div>
            </div>
          </div>

          {{-- === Description Field === --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Detailed Description <span class="text-danger">*</span></label>
            <textarea 
              name="description" 
              id="reportDescription"
              class="form-control"
              rows="5"
              maxlength="500"
              placeholder="Describe what happened in detail..."
              required></textarea>
            <small class="text-muted"><span id="charCount">0</span>/500 characters</small>
          </div>

          {{-- === Attachment Upload === --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Attach Screenshot or File (optional)</label>
            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.txt">
            <small class="text-muted">Supported formats: JPG, PNG, PDF (max 2MB)</small>
          </div>

          {{-- === Contact Info (Optional) === --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Your Contact Email (optional)</label>
            <input type="email" name="contact_email" class="form-control" placeholder="you@example.com">
          </div>

        </div>

        {{-- === Footer === --}}
        <div class="modal-footer bg-white">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane me-1"></i> Submit Report
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- === Character Counter Script === --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('reportDescription');
    const charCount = document.getElementById('charCount');
    textarea?.addEventListener('input', () => {
      charCount.textContent = textarea.value.length;
    });
  });
</script>
