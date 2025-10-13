@if(auth()->check())
<form id="sellerRegistrationForm" action="{{ route('farmers.storeSeller') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="modal fade" id="ModalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">

        {{-- Header --}}
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Register as a Seller</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        {{-- Stepper --}}
        <div class="px-4">
          <div class="fs-6 text-muted mb-2">Setup</div>
          <div class="reg-stepper position-relative mb-3">
            <div class="reg-line"></div>
            <div class="reg-step active" data-step="1">
              <span class="dot"></span><span class="label">Shop Information</span>
            </div>
            <div class="reg-step" data-step="2">
              <span class="dot"></span><span class="label">Business Information</span>
            </div>
            <div class="reg-step" data-step="3">
              <span class="dot"></span><span class="label">Submit</span>
            </div>
          </div>
        </div>

        <div class="modal-body pt-0">

          {{-- STEP 1: Shop Information --}}
          <div id="step-1" class="step-pane">
            <div class="mb-4">
              <label class="form-label fw-semibold">* Shop Name</label>
              <input type="text" name="shop_name" class="form-control" maxlength="30" placeholder="Your shop name" required>
              <div class="form-text">Max 30 characters</div>
            </div>

            {{-- Pickup Address (now inline, no modal) --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Pickup Address</label>

              {{-- Quick preview (auto-filled from fields below) --}}
              <input id="pickupPreview" name="pickup_address" class="form-control mb-3" type="text"
                     value="{{ old('pickup_address') }}" placeholder="Full address will appear here…" readonly>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="pickup_full_name" id="pickup_full_name" class="form-control" placeholder="Full name">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone Number</label>
                  <input type="text" name="pickup_phone" id="pickup_phone" class="form-control" placeholder="+63… / 09…">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Region Group</label>
                  <input type="text" name="pickup_region_group" id="pickup_region_group" class="form-control"
                         value="{{ old('pickup_region_group','North Luzon') }}">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Province</label>
                  <input type="text" name="pickup_province" id="pickup_province" class="form-control"
                         value="{{ old('pickup_province','Pangasinan') }}">
                </div>

                <div class="col-md-4">
                  <label class="form-label">City</label>
                  <select name="pickup_city" id="pickup_city" class="form-select">
                    <option value="">— Select City —</option>
                    {{-- Filled by JS from PANGASINAN list --}}
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Barangay</label>
                  <select name="pickup_barangay" id="pickup_barangay" class="form-select" disabled>
                    <option value="">— Select Barangay —</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Postal Code</label>
                  <input type="text" name="pickup_postal" id="pickup_postal" class="form-control" placeholder="e.g., 2432">
                </div>

                <div class="col-md-12">
                  <label class="form-label">Detail Address</label>
                  <textarea name="pickup_detail" id="pickup_detail" rows="2" class="form-control"
                            placeholder="House / street / landmark"></textarea>
                </div>

                <div class="col-12">
                  <iframe id="pickup_map" class="w-100 rounded border" height="220" loading="lazy"
                          referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
              </div>
            </div>

            {{-- Email (locked) --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">* Email</label>
              <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
            </div>
          </div>

          {{-- STEP 2: Business Information --}}
          <div id="step-2" class="step-pane d-none">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">* Business Type</label>
                <select name="business_type" class="form-select" required>
                  <option value="individual">Individual</option>
                  <option value="sole">Sole Proprietor</option>
                  <option value="corporation">Corporation</option>
                  <option value="cooperative">Cooperative</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Tin ID No. (optional)</label>
                <input type="text" name="tax_id" class="form-control" placeholder="TIN (if available)">
              </div>
            </div>

            <hr>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Government ID</label>
                {{-- file will be saved as gov_id_path in DB --}}
                <input type="file" name="gov_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                <div class="form-text">National ID, Driver’s License, etc. (JPG/PNG/PDF, max 4MB)</div>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">RSBSA</label>
                {{-- file will be saved as rsbsa_path in DB --}}
                <input type="file" name="rsbsa" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                <div class="form-text">Registry System for Basic Sectors in Agriculture</div>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Mayor’s Permit</label>
                {{-- file will be saved as mayors_permit_path in DB --}}
                <input type="file" name="mayors_permit" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                <div class="form-text">Upload a clear copy (optional)</div>
              </div>
            </div>
          </div>

          {{-- STEP 3: Submit / Review --}}
          <div id="step-3" class="step-pane d-none">
            <div class="mb-3">
              <h6 class="fw-semibold">Review</h6>
              <ul class="list-unstyled small" id="reviewList"></ul>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="termsCheck" required>
              <label class="form-check-label" for="termsCheck">
                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>.
              </label>
            </div>
          </div>

        </div>

        {{-- Footer buttons --}}
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary d-none" id="btnBack">Back</button>
          <button type="button" class="btn btn-primary" id="btnNext">Next</button>
          <button type="submit" class="btn btn-success d-none" id="btnSubmit" disabled>Submit</button>
        </div>

      </div>
    </div>
  </div>

  {{-- Terms modal --}}
  <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Terms & Conditions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body small">
          <p>These Terms and Conditions (“Terms”) constitute a legally binding agreement between Farmsmart (“the Platform,” “we,” “our,” or “us”) and the registered user (“Seller,” “you,” or “your”) governing your use of the Farmsmart platform and related services. By clicking “I Agree to the Terms & Conditions” or by continuing to use the platform, you acknowledge that you have read, understood, and agreed to be bound by these Terms.

1. Seller Registration and Obligations

1.1. The Seller agrees to provide complete, current, and accurate information during registration and to promptly update any information that may change.

1.2. The Seller affirms that all business permits, licenses, and certifications submitted are valid and issued by the appropriate government agencies.

1.3. The Seller shall be solely responsible for the products offered, including their legality, quality, safety, and conformity with applicable laws and regulations.

1.4. In the event of any misrepresentation or submission of falsified documents, the Seller shall receive a formal warning from the Platform. Repeated violations or failure to comply after such warning shall result in the suspension or termination of the Seller’s account without further notice.

2. Product Listings

2.1. The Seller agrees to post only lawful and authentic products, with accurate and truthful descriptions, pricing, and images.

2.2. The Platform strictly prohibits the sale of counterfeit, expired, illegal, or restricted items.

3. Transactions and Fulfillment

3.1. The Seller shall process and fulfill all confirmed orders in a timely and professional manner.

3.2. The Seller shall ensure proper packaging, shipment, and delivery of the product to the Buyer.

3.3. Failure to deliver or repeated order cancellations without just cause may result in penalties, suspension, or termination of the Seller’s account.

4. Fees and Payments

4.1. The Platform may impose transaction, service, or administrative fees as part of its operational policies.

4.2. The Platform shall facilitate payment processing through secure and approved channels.

4.3. The Platform reserves the right to withhold or reverse payments in cases involving fraud, disputes, chargebacks, or policy violations.

5. Data Privacy and Confidentiality

5.1. The Platform shall collect, process, and store Seller information in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules.

5.2. The Seller authorizes the Platform to use provided information for verification, communication, and transaction purposes.

5.3. The Platform shall exercise due diligence in safeguarding Seller information but shall not be held liable for breaches beyond its reasonable control.

6. Account Suspension and Termination

6.1. The Platform reserves the right to suspend or terminate any Seller account found to be in violation of these Terms or any applicable law.

6.2. The Seller may voluntarily request account termination, subject to settlement of all pending transactions and obligations.

6.3. Termination of the account does not exempt the Seller from liability for past actions or obligations incurred prior to termination.

7. Limitation of Liability

7.1. The Platform shall not be liable for any indirect, incidental, or consequential damages arising from the Seller’s use of the Platform.

7.2. The Seller acknowledges that the Platform acts solely as a facilitator between Sellers and Buyers and is not a party to any direct sale transaction.

7.3. The Seller agrees to indemnify and hold harmless the Platform from any claim, loss, or liability arising from the Seller’s acts, omissions, or product defects.

8. Amendments and Revisions

8.1. The Platform reserves the right to amend or revise these Terms at any time.

8.2. Any significant changes shall be communicated through email, system notifications, or public posting within the Platform.

8.3. Continued use of the Platform after such amendments constitutes acceptance of the revised Terms.

9. Governing Law and Jurisdiction

9.1. These Terms shall be governed by and construed in accordance with the laws of the Republic of the Philippines.

9.2. Any disputes arising out of or relating to these Terms shall be submitted to the proper courts of Pangasinan, to the exclusion of all other venues.

10. Acceptance

By proceeding with registration and clicking “I Agree to the Terms & Conditions”, you acknowledge that you have read, understood, and voluntarily agreed to these Terms and Conditions in their entirety.</p>
        </div>
      </div>
    </div>
  </div>

</form>
@endif

<style>
.reg-stepper{display:flex;gap:0;justify-content:space-between;align-items:center;padding:6px 0}
.reg-stepper .reg-step{position:relative;display:flex;flex-direction:column;align-items:center;gap:.25rem;flex:1 1 0}
.reg-stepper .reg-step .dot{width:12px;height:12px;border-radius:50%;background:#dee2e6;border:2px solid #dee2e6;z-index:2}
.reg-stepper .reg-step.active .dot,
.reg-stepper .reg-step.completed .dot{background:#dc3545;border-color:#dc3545}
.reg-stepper .reg-step .label{font-size:.9rem;color:#6c757d;text-align:center}
.reg-stepper .reg-step.active .label,
.reg-stepper .reg-step.completed .label{color:#212529}
.reg-stepper .reg-line{position:absolute;left:12px;right:12px;top:11px;height:2px;background:#e9ecef;z-index:1}
.step-pane{animation:fade .12s ease-in}
@keyframes fade{from{opacity:.6}to{opacity:1}}
</style>

{{-- Stepper / Review --}}
<script>
(() => {
  const steps = [1,2,3];
  let cur = 1;

  const panes   = { 1: document.getElementById('step-1'),
                    2: document.getElementById('step-2'),
                    3: document.getElementById('step-3') };
  const stepEls = [...document.querySelectorAll('.reg-stepper .reg-step')];
  const btnBack   = document.getElementById('btnBack');
  const btnNext   = document.getElementById('btnNext');
  const btnSubmit = document.getElementById('btnSubmit');
  const termsCheck = document.getElementById('termsCheck');

  function setStep(n){
    cur = n;
    steps.forEach(s => panes[s].classList.toggle('d-none', s !== n));
    stepEls.forEach((el,i) => {
      const s = i+1;
      el.classList.toggle('active', s===n);
      el.classList.toggle('completed', s<n);
    });
    btnBack.classList.toggle('d-none', n===1);
    btnNext.classList.toggle('d-none', n===3);
    btnSubmit.classList.toggle('d-none', n!==3);
  }

  function validStep1(){
    const name = document.querySelector('input[name="shop_name"]');
    return !!name && !!name.value.trim();
  }

  function buildReview(){
    const v = sel => document.querySelector(sel)?.value?.trim() || '';
    const list = document.getElementById('reviewList');

    const pickupSummary =
      v('#pickup_detail') ? [
        v('#pickup_detail'),
        v('#pickup_barangay'),
        v('#pickup_city'),
        v('#pickup_province'),
        v('#pickup_region_group'),
        v('#pickup_postal')
      ].filter(Boolean).join(', ')
      : (document.getElementById('pickupPreview')?.value?.trim() || '');

    const gov   = document.querySelector('input[name="gov_id"]')?.files?.[0]?.name || '—';
    const rsbsa = document.querySelector('input[name="rsbsa"]')?.files?.[0]?.name || '—';
    const mayor = document.querySelector('input[name="mayors_permit"]')?.files?.[0]?.name || '—';

    const items = [
      ['Shop Name', v('input[name="shop_name"]')],
      ['Pickup Address', pickupSummary || '—'],
      ['Email', '{{ auth()->user()->email }}'],
      ['Business Type', v('select[name="business_type"]') || '—'],
      ['Tax ID', v('input[name="tax_id"]') || '—'],
      ['Government ID file', gov],
      ['RSBSA file', rsbsa],
      ['Mayor’s Permit file', mayor],
    ];

    list.innerHTML = items
      .map(([k,val]) => `<li><strong>${k}:</strong> <span class="text-muted">${val || '—'}</span></li>`)
      .join('');
  }

  btnNext.addEventListener('click', () => {
    if (cur===1 && !validStep1()) return;
    if (cur===2) buildReview();
    setStep(Math.min(3, cur+1));
  });
  btnBack.addEventListener('click', () => setStep(Math.max(1, cur-1)));

  termsCheck?.addEventListener('change', () => {
    btnSubmit.disabled = !termsCheck.checked;
  });

  setStep(1);
})();
</script>

{{-- Inline pickup address logic (no modal) --}}
<script>
(() => {
  // Cities/Barangays for Pangasinan (extend as needed)
  const PANGASINAN = {
    'Alaminos City': {}, 'Dagupan City': {}, 'San Carlos City': {}, 'Urdaneta City': {},
    'Agno': {}, 'Aguilar': {}, 'Alcala': {}, 'Anda': {}, 'Asingan': {}, 'Balungao': {},
    'Bani': {}, 'Basista': {}, 'Bautista': {}, 'Bayambang': {}, 'Binalonan': {}, 'Binmaley': {},
    'Bolinao': {}, 'Bugallon': {}, 'Burgos': {}, 'Calasiao': {}, 'Dasol': {}, 'Infanta': {},
    'Labrador': {}, 'Laoac': {}, 'Lingayen': {}, 'Mabini': {}, 'Malasiqui': {}, 'Manaoag': {},
    'Mangaldan': { barangays: ['David','Anolid','Buenlag'] },
    'Mangatarem': {}, 'Mapandan': {}, 'Natividad': {}, 'Pozorrubio': {}, 'Rosales': {},
    'San Fabian': {}, 'San Jacinto': {}, 'San Manuel': {}, 'San Nicolas': {}, 'San Quintin': {},
    'Santa Barbara': {}, 'Santa Maria': {}, 'Santo Tomas': {}, 'Sison': {}, 'Sual': {},
    'Tayug': {}, 'Umingan': {}, 'Urbiztondo': {}, 'Villasis': {}
  };

  const $preview   = document.getElementById('pickupPreview');
  const $region    = document.getElementById('pickup_region_group');
  const $province  = document.getElementById('pickup_province');
  const $city      = document.getElementById('pickup_city');
  const $barangay  = document.getElementById('pickup_barangay');
  const $postal    = document.getElementById('pickup_postal');
  const $detail    = document.getElementById('pickup_detail');
  const $map       = document.getElementById('pickup_map');

  // Fill City options
  const cities = Object.keys(PANGASINAN).sort();
  cities.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c; opt.textContent = c;
    $city.appendChild(opt);
  });

  function setBarangaysFor(city) {
    const node = PANGASINAN[city] || {};
    const arr = Array.isArray(node.barangays) ? node.barangays : [];
    $barangay.innerHTML = '<option value="">— Select Barangay —</option>';
    if (arr.length) {
      $barangay.disabled = false;
      arr.forEach(b => {
        const o = document.createElement('option');
        o.value = b; o.textContent = b;
        $barangay.appendChild(o);
      });
    } else {
      $barangay.disabled = true;
    }
  }

  function assembleAddress() {
    return [
      ($detail.value || '').trim(),
      ($barangay.value || '').trim(),
      ($city.value || '').trim(),
      ($province.value || '').trim(),
      ($region.value || '').trim(),
      ($postal.value || '').trim()
    ].filter(Boolean).join(', ');
  }

  function updatePreviewAndMap() {
    const full = assembleAddress();
    $preview.value = full;
    const q = encodeURIComponent(full || ($province.value || 'Pangasinan') + ', Philippines');
    $map.src = `https://maps.google.com/maps?q=${q}&z=14&output=embed`;
  }

  $city.addEventListener('change', () => {
    setBarangaysFor($city.value);
    updatePreviewAndMap();
  });
  [$barangay, $postal, $detail, $region, $province].forEach(el => {
    el.addEventListener('input', updatePreviewAndMap);
    el.addEventListener('change', updatePreviewAndMap);
  });

  // Initial preview
  updatePreviewAndMap();
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form      = document.getElementById('sellerRegistrationForm');
  const btnSubmit = document.getElementById('btnSubmit');
  const modalEl   = document.getElementById('ModalCreate');

  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();                 // stop full page reload
    btnSubmit.disabled = true;

    try {
      const resp = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: new FormData(form)        // includes your files
      });

      const data = await resp.json().catch(() => ({}));

      if (!resp.ok || !data?.success) {
        const msg = data?.message
          || (data?.errors ? Object.values(data.errors).flat()[0] : null)
          || 'Something went wrong. Please try again.';
        alert(msg);
        btnSubmit.disabled = false;
        return;
      }

      // Close the modal
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.hide();

      // 🔽 EXACTLY HERE — update the launcher to "My Shop"
      const startBtn = document.getElementById('startSellingBtn'); // your trigger id
      if (startBtn) {
        startBtn.textContent = 'My Shop';
        startBtn.classList.remove('btn-outline-primary');
        startBtn.classList.add('btn-outline-success');
        startBtn.removeAttribute('data-bs-toggle');
        startBtn.removeAttribute('data-bs-target');

        if ('href' in startBtn) {
          startBtn.href = data.redirect_url || '{{ route("seller.dashboard") }}';
        } else {
          startBtn.onclick = () => window.location.href = data.redirect_url || '{{ route("seller.dashboard") }}';
        }
      }

      // Redirect (or comment this out if you prefer to stay)
      if (data.redirect_url) {
        window.location.href = data.redirect_url;
      } else {
        // window.location.reload();
      }

    } catch (err) {
      console.error(err);
      alert('Network error. Please try again.');
      btnSubmit.disabled = false;
    }
  });
});
</script>
