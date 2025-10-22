@if(auth()->check())
  <form id="sellerRegistrationForm" action="{{ route('farmers.storeSeller') }}" method="POST"
    enctype="multipart/form-data">
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
                <input type="text" name="shop_name" class="form-control" maxlength="30" placeholder="Your shop name"
                  required>
                <div class="form-text">Max 30 characters</div>
              </div>

              @php
                // Get logged-in user's first or default address
                $address = auth()->user()->addresses()->first(); 
              @endphp

              {{-- Pickup Address (now inline, no modal) --}}
              <div class="mb-4">
                <label class="form-label fw-semibold">Pickup Address</label>

                {{-- Quick preview (auto-filled from fields below) --}}
                <input id="pickupPreview" name="pickup_address" class="form-control mb-3" type="text" value="{{ old(
      'pickup_address',
      $address
      ? $address->full_name . ', ' .
      $address->floor_unit_number . ', ' .
      $address->barangay . ', ' .
      $address->city . ', ' .
      $address->province
      : ''
    ) }}" placeholder="Full address will appear here…" readonly>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="pickup_full_name" id="pickup_full_name" class="form-control"
                      placeholder="Full name" value="{{ old('pickup_full_name', $address->full_name ?? '') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="pickup_phone" id="pickup_phone" class="form-control" placeholder="+63… / 09…"
                      value="{{ old('pickup_phone', $address->mobile_number ?? '') }}">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Region Group</label>
                    <input type="text" name="pickup_region_group" id="pickup_region_group" class="form-control"
                      value="{{ old('pickup_region_group', 'North Luzon') }}">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Province</label>
                    <input type="text" name="pickup_province" id="pickup_province" class="form-control"
                      value="{{ old('pickup_province', $address->province ?? 'Pangasinan') }}">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">City</label>
                    <select name="pickup_city" id="pickup_city" class="form-select">
                      <option value="">— Select City —</option>
                      @if(!empty($address?->city))
                        <option value="{{ $address->city }}" selected>{{ $address->city }}</option>
                      @endif
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Barangay</label>
                    <select name="pickup_barangay" id="pickup_barangay" class="form-select" {{ empty($address?->barangay) ? 'disabled' : '' }}>
                      <option value="">— Select Barangay —</option>
                      @if(!empty($address?->barangay))
                        <option value="{{ $address->barangay }}" selected>{{ $address->barangay }}</option>
                      @endif
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="pickup_postal" id="pickup_postal" class="form-control"
                      placeholder="e.g., 2432">
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
                  <input type="file" name="gov_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                  <div class="form-text">National ID, Driver’s License, etc. (JPG/PNG/PDF, max 4MB)</div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">RSBSA</label>
                  {{-- file will be saved as rsbsa_path in DB --}}
                  <input type="file" name="rsbsa" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                  <div class="form-text">Registry System for Basic Sectors in Agriculture</div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Mayor’s Permit</label>
                  {{-- file will be saved as mayors_permit_path in DB --}}
                  <input type="file" name="mayors_permit" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
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
            <h4 class="fw-bold text-center mb-4">FARMSMART — Seller Terms and Conditions</h4>

            <p>These Terms and Conditions (“Terms”) constitute a legally binding agreement between
              <strong>FarmSmart</strong> (“the Platform,” “we,” “our,” or “us”) and the registered user (“Seller,” “you,”
              or “your”) governing your use of the FarmSmart platform and related services.
            </p>

            <p>By clicking <strong>“I Agree to the Terms &amp; Conditions”</strong> or by continuing to use the platform,
              you acknowledge that you have read, understood, and agreed to be bound by these Terms.</p>

            <h5 class="mt-4 fw-semibold">1. Seller Registration and Obligations</h5>
            <ul>
              <li>The Seller agrees to provide complete, current, and accurate information during registration and to
                promptly update any information that may change.</li>
              <li>The Seller affirms that all business permits, licenses, and certifications submitted are valid and
                issued by the appropriate government agencies.</li>
              <li>The Seller shall be solely responsible for the products offered, including their legality, quality,
                safety, and conformity with applicable laws and regulations.</li>
              <li>In the event of any misrepresentation or submission of falsified documents, the Seller shall receive a
                formal warning. Repeated violations or failure to comply shall result in suspension or termination of the
                Seller’s account.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">2. Product Listings</h5>
            <ul>
              <li>The Seller agrees to post only lawful and authentic products, with accurate and truthful descriptions,
                pricing, and images.</li>
              <li>The Platform strictly prohibits the sale of counterfeit, expired, illegal, or restricted items.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">3. Transactions and Fulfillment</h5>
            <ul>
              <li>The Seller shall process and fulfill all confirmed orders in a timely and professional manner.</li>
              <li>The Seller shall ensure proper packaging, shipment, and delivery of the product to the Buyer.</li>
              <li>Failure to deliver or repeated cancellations without just cause may result in penalties, suspension, or
                termination.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">4. Fees and Payments</h5>
            <ul>
              <li>The Platform may impose transaction, service, or administrative fees as part of its operational
                policies.</li>
              <li>Payments shall be processed through secure and approved channels.</li>
              <li>The Platform reserves the right to withhold or reverse payments in cases involving fraud, disputes, or
                violations.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">5. Data Privacy and Confidentiality</h5>
            <ul>
              <li>The Platform shall collect, process, and store Seller information in accordance with the <strong>Data
                  Privacy Act of 2012 (RA 10173)</strong>.</li>
              <li>The Seller authorizes the Platform to use information for verification, communication, and transaction
                purposes.</li>
              <li>The Platform shall exercise due diligence in safeguarding information but shall not be liable for
                breaches beyond its reasonable control.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">6. Account Suspension and Termination</h5>
            <ul>
              <li>The Platform reserves the right to suspend or terminate accounts found in violation of these Terms or
                any applicable law.</li>
              <li>The Seller may voluntarily request account termination, subject to settlement of pending transactions
                and obligations.</li>
              <li>Termination does not exempt the Seller from liability for past actions prior to termination.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">7. Limitation of Liability</h5>
            <ul>
              <li>The Platform shall not be liable for any indirect, incidental, or consequential damages arising from use
                of the Platform.</li>
              <li>The Seller acknowledges that the Platform acts solely as a facilitator and is not a party to any direct
                sale transaction.</li>
              <li>The Seller agrees to indemnify and hold harmless the Platform from any claim, loss, or liability
                resulting from Seller’s acts, omissions, or product defects.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">8. Amendments and Revisions</h5>
            <ul>
              <li>The Platform reserves the right to amend or revise these Terms at any time.</li>
              <li>Significant changes shall be communicated through email, notifications, or public posting within the
                Platform.</li>
              <li>Continued use of the Platform after such amendments constitutes acceptance of the revised Terms.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">9. Governing Law and Jurisdiction</h5>
            <ul>
              <li>These Terms shall be governed by and construed in accordance with the laws of the <strong>Republic of
                  the Philippines</strong>.</li>
              <li>Any disputes shall be submitted to the proper courts of <strong>Pangasinan</strong>, to the exclusion of
                all other venues.</li>
            </ul>

            <h5 class="mt-4 fw-semibold">10. Acceptance</h5>
            <p>By proceeding with registration and clicking <strong>“I Agree to the Terms &amp; Conditions”</strong>, you
              acknowledge that you have read, understood, and voluntarily agreed to these Terms and Conditions in their
              entirety.</p>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </form>
@endif

<style>
  .reg-stepper {
    display: flex;
    gap: 0;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0
  }

  .reg-stepper .reg-step {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .25rem;
    flex: 1 1 0
  }

  .reg-stepper .reg-step .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #dee2e6;
    border: 2px solid #dee2e6;
    z-index: 2
  }

  .reg-stepper .reg-step.active .dot,
  .reg-stepper .reg-step.completed .dot {
    background: #dc3545;
    border-color: #dc3545
  }

  .reg-stepper .reg-step .label {
    font-size: .9rem;
    color: #6c757d;
    text-align: center
  }

  .reg-stepper .reg-step.active .label,
  .reg-stepper .reg-step.completed .label {
    color: #212529
  }

  .reg-stepper .reg-line {
    position: absolute;
    left: 12px;
    right: 12px;
    top: 11px;
    height: 2px;
    background: #e9ecef;
    z-index: 1
  }

  .step-pane {
    animation: fade .12s ease-in
  }

  @keyframes fade {
    from {
      opacity: .6
    }

    to {
      opacity: 1
    }
  }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // ====== Step Setup ======
  const steps = [
    document.getElementById("step-1"),
    document.getElementById("step-2"),
    document.getElementById("step-3")
  ];

  const btnNext = document.getElementById("btnNext");
  const btnBack = document.getElementById("btnBack");
  const btnSubmit = document.getElementById("btnSubmit");
  const stepDots = document.querySelectorAll(".reg-stepper .reg-step");
  const form = document.querySelector("#ModalCreate form") || document.getElementById("sellerRegistrationForm");
  const termsCheck = document.getElementById("termsCheck");

  let currentStep = 0;

  // ====== Alert box (top of modal body) ======
  const alertBox = document.createElement("div");
  alertBox.className = "alert alert-warning small mt-3";
  alertBox.style.display = "none";
  alertBox.innerHTML = "⚠️ Please complete all required fields before continuing.";
  document.querySelector("#ModalCreate .modal-body").prepend(alertBox);

  // ====== Step Display ======
  function showStep(index) {
    steps.forEach((step, i) => {
      step.classList.toggle("d-none", i !== index);
      stepDots[i]?.classList.toggle("active", i === index);
      stepDots[i]?.classList.toggle("completed", i < index);
    });

    btnBack.classList.toggle("d-none", index === 0);
    btnNext.classList.toggle("d-none", index === steps.length - 1);
    btnSubmit.classList.toggle("d-none", index !== steps.length - 1);
    alertBox.style.display = "none";
  }

  // ====== Validation ======
  function validateStep() {
    const currentPane = steps[currentStep];
    const requiredFields = currentPane.querySelectorAll("[required]");
    let isValid = true;

    requiredFields.forEach(field => {
      let ok = true;

      if (field.type === "file") {
        ok = field.files && field.files.length > 0;
      } else if (field.type === "checkbox" || field.type === "radio") {
        const group = currentPane.querySelectorAll(`[name='${field.name}']`);
        ok = [...group].some(f => f.checked);
      } else {
        ok = field.value.trim() !== "";
      }

      if (!ok) {
        isValid = false;
        field.classList.add("is-invalid");
      } else {
        field.classList.remove("is-invalid");
      }

      // Auto-remove red border + hide alert when user fixes it
      const clearInvalid = () => {
        field.classList.remove("is-invalid");
        if (![...currentPane.querySelectorAll(".is-invalid")].length) {
          alertBox.style.display = "none";
        }
      };
      field.addEventListener("input", clearInvalid);
      field.addEventListener("change", clearInvalid);
    });

    if (!isValid) {
      alertBox.style.display = "block";
      alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    return isValid;
  }

  // ====== Review Builder ======
  function buildReview() {
    const v = sel => document.querySelector(sel)?.value?.trim() || "";
    const list = document.getElementById("reviewList");

    const pickupSummary =
      v("#pickup_detail")
        ? [v("#pickup_detail"), v("#pickup_barangay"), v("#pickup_city"),
           v("#pickup_province"), v("#pickup_region_group"), v("#pickup_postal")]
            .filter(Boolean).join(", ")
        : (document.getElementById("pickupPreview")?.value?.trim() || "");

    const gov = document.querySelector("input[name='gov_id']")?.files?.[0]?.name || "—";
    const rsbsa = document.querySelector("input[name='rsbsa']")?.files?.[0]?.name || "—";
    const mayor = document.querySelector("input[name='mayors_permit']")?.files?.[0]?.name || "—";

    const items = [
      ["Shop Name", v("input[name='shop_name']")],
      ["Pickup Address", pickupSummary || "—"],
      ["Email", "{{ auth()->user()->email }}"],
      ["Business Type", v("select[name='business_type']") || "—"],
      ["Tax ID", v("input[name='tax_id']") || "—"],
      ["Government ID file", gov],
      ["RSBSA file", rsbsa],
      ["Mayor’s Permit file", mayor],
    ];

    list.innerHTML = items
      .map(([k, val]) => `<li><strong>${k}:</strong> <span class="text-muted">${val}</span></li>`)
      .join("");
  }

  // ====== Navigation ======
  btnNext.addEventListener("click", () => {
    if (!validateStep()) return;

    if (currentStep === 1) buildReview(); // Step 2 → 3
    if (currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  });

  btnBack.addEventListener("click", () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  form?.addEventListener("submit", e => {
    if (!validateStep()) e.preventDefault();
  });

  termsCheck?.addEventListener("change", () => {
    btnSubmit.disabled = !termsCheck.checked;
  });

  showStep(currentStep);
});
</script>

{{-- Inline pickup address logic (no modal) --}}
<script>
  (() => {
    // Cities/Barangays for Pangasinan (extend as needed)
    const PANGASINAN = {
      'Alaminos City': { barangays: ['Alos', 'Amandiego', 'Arawan', 'Bail', 'Balogo', 'Banban', 'Balangobong', 'Basen', 'Bawas', 'Baybay', 'Bula', 'Cabungcalan', 'Caburque', 'Cagat', 'Calabanga', 'Calantipayan', 'Caranuan', 'Dapdap', 'Dimalawa', 'Don Don', 'In Salinas', 'Landas', 'Lima', 'Linmansangan', 'Lone', 'Lubong', 'Lucap', 'Magsaysay', 'Malig', 'Matag', 'Palamis', 'Pandac', 'Pangapian', 'Pao', 'Paraoir', 'Patbo', 'Payapay', 'Poblacion', 'Pudoc', 'Quinot', 'Sabangan', 'Sabangan East', 'Sabangan West', 'San Jose', 'San Miguel', 'Santa Rita', 'Saoang', 'Sayak', 'Tabor', 'Tampac', 'Tangcaran', 'Telbang', 'Tepat', 'Tikas', 'Tindog', 'Turong', 'Victoria'] },
      'Dagupan City': { barangays: ['Bacayao Norte', 'Bacayao Sur', 'Barangay I', 'Barangay II', 'Barangay IV', 'Bolosan', 'Bonuan Binloc', 'Bonuan Boquig', 'Bonuan Gueset', 'Calmay', 'Carael', 'Caranglaan', 'Herrero', 'La Sip Chico', 'La Sip', 'Grande', 'Lomboy', 'Lucao', 'Malued', 'Mama Lingling', 'Mangin', 'Mayombo', 'Pantal', 'Poblacion Oeste', 'Barangay I', 'Pogo Chico', 'Pogo Grande', 'Pugaro Suit', 'Salapingao', 'Salisay', 'Tambac', 'Tapuac', 'Tebeng'] },
      'San Carlos City': { barangays: ['Abanon', 'Agdao', 'Anciano T. Tandoc', 'Balite Sur', 'Balite Norte', 'Balingueo', 'Bayanihan', 'Bogaoan', 'Bolingit', 'Caoayan Kiling', 'Cobol', 'Coliling', 'Cruz', 'Dipalo', 'Guelew', 'Ilang', 'Inerangan', 'Libertad', 'Lilimbo', 'Longos', 'Lucban Paoay', 'Mabalbalino', 'Mancagayca', 'Matagdem', 'Mitolong', 'Narvacan East', 'Narvacan West', 'Palaris', 'Palaming', 'Pandayan', 'Pangalangan', 'Pangel', 'Paitan-Panoypoy', 'Parayao', 'Payapa', 'Payar', 'Poblacion East', 'Poblacion West', 'Roxas', 'Salinap', 'San Juan', 'San Pedro Taloy', 'Sapang', 'Sawang', 'Talang', 'Tamayan', 'Tandang Sora', 'Tigui', 'Turac'] },
      'Urdaneta City': { barangays: ['Anonas', 'Bactao', 'Bayaoas', 'Bolaoen', 'Cabaruan', 'Cabuloan', 'Calegu', 'Camantiles', 'Casantaan', 'Catablan', 'Cayambanan', 'Consuelo', 'Dilan Paurido', 'Labit Proper', 'Labit West', 'Maabay', 'Macalong', 'Nancalobasaan', 'Nangapugan', 'Palina East', 'Palina West', 'Poblacion', 'San Jose', 'San Vicente', 'Santa Lucia', 'Santo Domingo', 'Sugcong', 'Tipuso',] },
      'Agno': { barangays: ['Allabon', 'Alumina', 'Bayan East', 'Bayan West', 'Bega', 'Boboy', 'Dagupan', 'Gayusan', 'Guisay', 'Macaboboni', 'Poblacion East', 'Poblacion West', 'San Juan', 'Tupa'] },
      'Aguilar': { barangays: ['Bacante', 'Bale', 'Bawer', 'Baybay', 'Bita', 'Bongar', 'Cabayaoasan', 'Caguray', 'Calao', 'Calumbaya', 'Carmen East', 'Carmen West', 'Dapdappig', 'Dapla', 'Fisac', 'Gahard', 'Gandam', 'Guinbayan', 'Las-ud', 'Licsi', 'Liliao', 'Lubing', 'Mabini', 'Macabato', 'Malamin', 'Malupa', 'Nagsingcaoan', 'Naguelguel', 'Nangalisan', 'Poblacion', 'Pudoc', 'Pugo', 'Quimmarayan', 'Rang-ay', 'Sabangan', 'San Antonio', 'San Jose', 'Santa Cruz', 'Santa Maria', 'Sao Miguel', 'Sayak', 'Talelet', 'Tampac', 'Tao', 'Tondol', 'Tungao'] },
      'Alcala': { barangays: ['Anulid', 'Apalong', 'Bacquigue', 'Bagong Anac', 'Balingueo', 'Baybay Lopez', 'Baybay Polong', 'Botao', 'Buenavista', 'Bulaoen East', 'Bulaoen West', 'Cabcaburao', 'Calaocan East', 'Calaocan West', 'Carmen East', 'Carmen West', 'Dipalo', 'Guitna', 'Kisikis', 'Laoac East', 'Laoac West', 'Macayo', 'Pagbangkeruan', 'Paregu-eg East', 'Paregu-eg West', 'Poblacion East', 'Poblacion West', 'San Juan East', 'San Juan West', 'San Nicolas East', 'San Nicolas West', 'San Pedro Apartado', 'San Pedro IlI', 'San Vicente', 'Vacante'] },
      'Anda': { barangays: ['Awile', 'Bila', 'Boglai', 'Dolaoan', 'Macaleeng', 'Macapandan', 'Mal-ong', 'Manaoag', 'Poblacion', 'Sablig', 'San Jose', 'Siapar', 'Talogtog', 'Tandoc'] },
      'Asingan': { barangays: ['Arboleda', 'Alog', 'Ambonao', 'Angayan Norte', 'Angayan Sur', 'Antongalon', 'Apotol', 'Baay', 'Baguileo', 'Balandra', ' Bantog ', 'Baro', 'Bobonan', 'Cabaruan', 'Calepaan', 'Carosucan Norte', 'Carosucan Sur', 'Coldit', 'Domanpot', ' Dupac ', 'Macalong', 'Palaris', 'Poblacion East', 'Poblacion West', 'San Juan', 'Toboy'] },
      'Balungao': { barangays: [' Angayan ', ' Bangsal ', ' Banzon ', 'Bongalon', 'Buenavista', 'Bugallon', ' Cabongaoan ', ' Calabaan ', ' Capulaan ', ' Esmeralda ', ' Kita-kita ', ' Magolong ', ' Malabong ', ' Marmaray ', 'Maticaa', 'Narra', 'Niog', 'Orence', 'Padre Galo', 'Poblacion', 'Rajal Centro', 'Rajal Norte', 'Rajal Sur', 'San Andres', 'San Aurelio 1st', 'San Aurelio 2nd', 'San Joaquin', 'San Julian', 'San Leon', 'San Marcelino', 'San Miguel', 'San Raymundo', 'San Vicente', 'Santa Barbara', 'Santo Niño', 'Sumera', 'Villar Pereda'] },
      'Bani': { barangays: ['Aporao', 'Arwas', 'Balerin', 'Bani', 'Binacag', 'Cabaruyan', 'Colayo', 'Dacap Norte', 'Dacap Sur', 'Garrita', 'Luac', 'Macabit', 'Masidem Norte', 'Masidem Sur', 'Nagsaing', 'Olanen', 'Paaralan', ' Poblacion ', 'Quibuar', 'San Jose', 'San Miguel', 'San Simon', 'San Vicente', 'Tiep'] },
      'Basista': { barangays: ['Anambongan', 'Baguinday', 'Baluyot', 'Bautista', 'Bayoyong', 'Cabeldatan', 'Calbayog', 'Cayoocan', 'Dumayas', 'Mapolopolo', 'Nangalisan', 'Nansebacan', 'Olea', 'Palma', 'Patacbo', 'Poblacion', 'San Carlos', 'Sinilian 1st', 'Sinilian 2nd'] },
      'Bautista': { barangays: ['Artacho', 'Bautista', 'Cabuaan', 'Cacandongan', 'Diaz', 'Nandacan', 'Poblacion East', 'Poblacion West', 'Sinabaan', 'Vacante'] },
      'Bayambang': { barangays: ['Alinggan', 'Ambayat I', 'Ambayat II', 'Bacariza', 'Balaybuaya', 'Banaban', 'Banas', 'Bani', 'Batangcaoa', 'Beleng', 'Bical Norte', 'Bical Sur', 'Buenlag 1st', 'Buenlag 2nd', 'Cadre Site', 'Carungay', 'Caturay', 'Duera', 'Dusoc', 'Hermosa', 'Idong', 'Inanlorenzana', 'Inirangan', 'Irineo Villar (Cabaruyan)', 'Manambong Norte', 'Manambong Parte', 'Manambong Sur', 'Mangayao', 'Mangatarem', 'Nalsian Norte', 'Nalsian Sur', 'Pangdel', 'Pantol', 'Paragos', 'Poblacion Zone I', 'Poblacion Zone II', 'Poblacion Zone III', 'Poblacion Zone IV', 'Poblacion Zone V', 'Pugo', 'San Gabriel 1st', 'San Gabriel 2nd', 'San Vicente', 'Sangcagulis', 'Sanlibo', 'Sapang', 'Tamaro', 'Tambac', 'Tococ East', 'Tococ West', 'Warding'] },
      'Binalonan': { barangays: ['Balangobong', 'Bued', 'Bugayong', 'Camangaan', 'Canarvacanan', 'Capas', 'Cili', 'Dumayat', 'Linmansangan', 'Mangcasuy', 'Moreno', 'Pasileng Norte', 'Pasileng Sur', 'Poblacion', 'San Felipe Central', 'San Felipe Sur', 'San Pablo', 'Santa Catalina', 'Santa Maria Norte', 'Santa Maria Sur', 'Santiago', 'Santo Niño', 'Sumague', 'Tabuyoc', 'Vacante'] },
      'Binmaley': { barangays: ['Amancoro', 'Balagan', 'Balingasay', 'Buenlag', 'Calit', 'Caloocan Norte', 'Caloocan Sur', 'Camaley', 'Canaoalan', 'Guelew', 'Linoc', 'Manat', 'Nagpandayan', 'Naguilayan East', 'Naguilayan West', 'Pangascasan', 'Patalan', 'Pototan', 'Sabangan', 'Salapingao', 'San Gonzalo', 'San Isidro Norte', 'San Isidro Sur', 'Tombor'] },
      'Bolinao': { barangays: ['Arnedo', 'Balingasay', 'Binabalian', 'Cabungan', 'Catuday', 'Concordia (Poblacion)', 'Culang', ' Dewey ', 'Estanza', 'Germinal (Poblacion)', 'Gubayan', 'Luna (Poblacion)', 'Patar', 'Pilar', 'Poblacion', 'Samang Norte', 'Samang Sur', 'Taliwara', 'Tara', 'Victory'] },
      'Bugallon': { barangays: ['Angarian', 'Asinan', 'Baguinday', 'Balegong', 'Bangi', 'Boglongan', 'Boned', 'Buenlag', 'Cabayaoasan', 'Cabualan', 'Calantipay', 'Carayungan', 'Gueset', 'Laguit Padilla', 'Magtaking', 'Manlocboc', 'Pantar', 'Poblacion', 'Portic', 'Salomague', 'Samat', 'Talogtog', 'Tonton', 'Umanday'] },
      'Burgos': { barangays: ['Anapao (Bur)', 'Antique', 'Bilis', 'Cabcaburao', 'Ilio-ilio (Iliw-iliw)', 'Inerangan', 'New Poblacion', 'Old Poblacion', 'Palogpoc', 'Pangalangan', 'Papallasen', 'Poblacion', 'Pogoruac', 'San Miguel', 'San Pascual', 'San Vicente', 'Sapa Grande', 'Taguitic'] },
      'Calasiao': { barangays: ['Ambon', 'Bacao', 'Baguio', 'Bolong', 'Bonifacio', 'Bued', 'Calaocan', 'Caranglaan', 'Dinalaoan', 'Kingking', 'Las Attaras', 'Longboan', 'Lubang', 'Malabago', 'Nailan', 'Nancalobasaan', 'Nalsian', 'Pangaoan', 'Parasio', 'Poblacion East', 'Poblacion West', 'Pugo', 'Rizal', 'San Miguel', 'San Vicente', 'Talba', 'Vacante'] },
      'Dasol': { barangays: ['Alacayao', 'Ambon', 'Anga', 'Bagongdaw', 'Bonbonon', 'Cabanas', 'Colosas', 'Dasol', 'Eguia', 'Gais-Guipe', 'Hermosa', 'Macalang', 'Malapas', 'Masi', 'Osmeña', 'Pangapisan', 'Poblacion', 'Ranao', 'San Vicente', 'Sangla', 'Tambobong'] },
      'Infanta': { barangays: ['Babuyan', 'Bagoong', 'Balayang', 'Binday', 'Botigue', 'Catambacan', 'Colambot', 'Dungay', 'Inmalog', 'Maya', 'Naguilayan', 'Nantangalan', 'Poblacion', 'Potot', 'Tagudin', 'Vitol'] },
      'Labrador': { barangays: ['Baculong', 'Bongalon', 'Bolo', 'Ilolong', 'Laois', 'Longa', 'Magsaysay', 'Poblacion', 'Poyao', 'San Jose', 'San Jose Norte', 'San Roque', 'San Vicente', 'Unip'] },
      'Laoac': { barangays: ['Anis', 'Balligi', 'Banuar', 'Botique', 'Cabilaoan', 'Calaocan', 'Domingo Alarcio', 'Ilolong', 'Inlog', 'Labayug', 'Leleng', 'Ligua', 'Longa', 'Manila', 'Poblacion', 'Talogtog', 'Turac'] },
      'Lingayen': { barangays: ['Ali We Kwek', 'Baay', 'Balangobong', 'Balococ', 'Bantayan', 'Basing', 'Ca Pandanan', 'Domalandan Center', 'Domalandan East', 'Domalandan West', 'Dorongan', 'Dulag', 'Estanza', 'La Sip', 'Libsong East', 'Libsong West', 'Malawa', 'Malimpuec', 'Maniboc', 'Matalava', 'Naguelguel', 'Namolan', 'Pangapisan North', 'Pangapisan Sur', 'Poblacion', 'Quibaol', 'Rosario', 'Sabangan', 'Talogtog', 'Tonton', 'Tumbar', 'Wawa'] },
      'Mabini': { barangays: ['Abad Santos', 'Alegria', 'T. Arlan', 'Bailan', 'Garcia', 'Libertad', 'Mabini', 'Mabuhay', 'Magsaysay', 'Rizal', 'Tang Bo'] },
      'Malasiqui': { barangays: ['Agdao', 'Aligui', 'Amacalan', 'Anolid', 'Apaya', 'Asin East', 'Asin West', 'Bacundao East', 'Bacundao West', 'Balite', 'Banawang', 'Bani', 'Bocacliw', 'Bocboc East', 'Bocboc West', 'Bolaoit', 'Buenlag East', 'Buenlag West', 'Cabalitian', 'Cainan', 'Canan Norte', 'Canan Sur', 'Cawayan Bugtong', 'Colayo', 'Dampay', 'Guilig', 'Ingalagala', 'Lacanlacan East', 'Lacanlacan West', 'Lipper', 'Longalong', 'Loqueb Este', 'Loqueb Norte', 'Loqueb Sur', 'Malabac', 'Mancilang', 'Matolong', 'Palapar Norte', 'Palapar Sur', 'Pasima', 'Payar', 'Polong Norte', 'Polong Sur', 'Potiocan', 'San Julian', 'Talospatang', 'Tomling', 'Ungib', 'Villacorta'] },
      'Manaoag': { barangays: ['Acao', 'Aglipay', 'Aloragat', 'Anas', 'Apalep', 'Baguinay', 'Barang', 'Baritao', 'Bisal', 'Bongal', 'Cabalitian', 'Cabaruan', 'Calamagui', 'Caramutan', 'Lelemaan', 'Licsi', 'Lipit Norte', 'Lipit Sur', 'Longalong', 'Matolong', 'Mermer', 'Nalsian', 'Oraan East', 'Oraan West', 'Pantal', 'Pao', 'Parian', 'Poblacion', 'Pugaro', 'San Inocencio', 'San Jose', 'San Ramon', 'San Roque', 'San Vicente', 'Santa Ines', 'Santa Maria', 'Tabora East', 'Tabora West', 'Tebuel', 'Vinalesa'] },
      'Mangaldan': { barangays: ['Alitaya', 'Amansangan East', 'Amansangan West', 'Anolid', 'Banaoang', 'Bongalon', 'Buenlag', 'David', 'Gueguesangen', 'Guiguilonen', 'Inlambo', 'Lanas', 'Landas Macayug', 'Lomboy', 'Macayug', 'Malabago', 'Navaluan', 'Nibaliw Central', 'Nibaliw East', 'Nibaliw West', 'Osiac', 'Poblacion', 'Salay', 'Talogtog', 'Tolonguat'] },
      'Mapandan': { barangays: ['Amano Diaz', 'Aserda', 'Balolong', 'Banaoang', 'Bolaoen', 'Cabalitian', 'Golden Sea Mobile Village', 'Guilig', 'Imbo', 'Luyan (Luyan East)', 'Nilombot', 'Poblacion', 'Primicias', 'Santa Maria', 'Torres'] },
      'Mangatarem': { barangays: ['Andangin', 'Arellano St.', 'Auditorio', 'Baculong Norte', 'Baculong Sur', 'Banaoang', 'Bayanihan', 'Benteng Norte', 'Benteng Sur', 'Bongliw', 'Bueno', 'Bunlalacao', 'Burgos St.', 'Cabangaran', 'Cabaruan', 'Cacaritan', 'Calabayan', 'Calzada', 'Caravilla', 'Casantiagoan', 'Castillejos', 'Catarataraan', 'Cawayan Bugtong', 'Cayanga', 'Cortes', 'Diaw', 'Estacion St.', 'General Luna St.', 'Guilig', 'Jackpot', 'Lawak Langka', 'Linmansangan', 'Lomboy', 'Luna St.', 'Manaoac', 'Maravilla', 'Maria St.', 'Navalas', 'Nipa', 'Nisom St.', 'Orence', 'Pacalat', 'Palaris', 'Palayan East', 'Palayan West', 'Pangascasan', 'Poblacion', 'Polintay', 'Quetegan St.', 'Ramos St.', 'Rang-Ay St.', 'Revolucion St.', 'Roxas St.', 'Salavante', 'San Antonio', 'San Juan Arao', 'San Roque', 'Santa Barbara St.', 'Santa Cruz St.', 'Santo Niño', 'Sauz', 'Sinapaoan', 'Tagac', 'Talogtog', 'Tococ Barikir', 'Torres Bugallon'] },
      'Natividad': { barangays: ['Batchelor East', 'Batchelor West', 'Burgos ( formerly Calamagui )', ' Cacabugaoan ', 'Canarem', ' Luna ', ' Poblacion East ', ' Poblacion West ', ' Salud ', ' San Eugenio ', ' San Macario Norte', ' San Macario Sur ', ' San Maximo ', ' Sinaoan East ', ' Sinaoan West ', ' Sudlon ( formerly Digdig )', 'Turac'] },
      'Pozorrubio': { barangays: [' Agat ', ' Amagbagan ', ' Ambalangan ', ' Anao ', ' Bagoong ', ' Balacag ', ' Banding ', ' Bangar ', ' Batakil ', ' Bobonan ', ' Cablong ', ' Casanayan ', ' Kaong ', ' Imboas ', ' Inoman ', ' Ligayat ', ' Maambal ', ' Malasin ', ' Nagsimbaan ', ' Narvacan I ', ' Narvacan II ', ' Palacpalac ', ' Palguyod ', ' Poblacion District I ', ' Poblacion District II ', ' Poblacion District III ', ' Poblacion District IV ', ' Rosario ', ' Sugcong ', ' Tulayan ', ' Villegas '] },
      'Rosales': { barangays: [' Acop ', ' Bakitbakit ', ' Balingcanaway ', ' Cabalaoangan Norte ', ' Cabalaoangan Sur ', ' Camangaan ', ' Capitan Tomas ', ' Carmay ', ' Casanicolasan ', ' Coliling ', ' Don Antonio Village ', ' Guiling ', ' Laoac ', ' Palakipak ', ' Pangaoan ', ' Pindahan ', ' Rabago ', ' Rizal ', ' San Bartolome ', ' San Cristobal ', ' San Luis ', ' San Martin ', ' San Pedro East ', ' San Pedro West ', ' San Roque ', ' San Vicente ', ' San Antonio ', ' San Ignacio ', ' San Isidro ', ' San Jose ', ' San Juan ', ' San Manuel ', ' San Miguel ', ' San Nicolas ', ' San Pio ', ' Santa Barbara ', ' Santa Maria ', ' Santa Monica '] },
      'San Fabian': { barangays: ['Angio', ' Asao ', ' Baay ', ' Bacao ', ' Balingueo ', ' Banaoang ', ' Binday ', ' Bolasi ', ' Cabaruan ', ' Cayanga ', ' Colisao ', ' Gomotoc ', ' Inmalog ', ' Inapalan ', ' Inarangan ', ' Inmalobo ', ' Lekep Butao ', ' Lipit Norte ', ' Lipit Sur ', ' Longalong ', ' Mabilao ', ' Nibaliw ', ' Palapad ', ' Poblacion ', ' Rabon ', ' Salay ', ' Tempra ', ' Tiblong '] },
      'San Jacinto': { barangays: [' Bagong ', ' Balasiao ', ' Cababuyan ', ' Calaguiman ', ' Casibong ', ' Imbo ', ' Labit ', ' Lasing ', ' Macayug ', ' Mamarlao ', ' Nalsian ', ' Paldong ', ' Pozo ', ' Santo Tomas ', ' Tagumising '] },
      'San Manuel': { barangays: [' Agno ', ' Balingasay ', ' Baracbac ', ' Cabaritan ', ' Cabatuan ', ' Cabilocaan ', ' Colayo ', ' Danac ', ' Don Matias ', ' Guiset Norte ', ' Guiset Sur ', ' Lapalo ', ' Narra ', ' Pacpaco ', ' Paraiso ', ' Pao ', ' San Bonifacio ', ' San Francisco ', ' San Roque ', ' Santa Cruz '] },
      'San Nicolas': { barangays: [' Baras ', ' Cabalitian ', ' Cacabugaoan ', ' Calanutian ', ' Camindoroan ', ' Casili ', ' Catuguing ', ' Malico ', ' Nining ', ' Poblacion ', ' Salpad ', ' San Roque ', ' Santa Maria ', ' Santo Cristo ', ' Sobol ', ' Tagudin '] },
      'San Quintin': { barangays: [' Alac ', ' Balayao ', ' Banawang ', ' Cabangaran ', ' Cabulalaan ', ' Caronoan ', ' Casantamariaan ', ' Gonzalo ', ' Labuan ', ' Laguit ', ' Maasin ', ' Mantacdang ', ' Nangapugan ', ' Palasigui ', ' Poblacion ', ' Ungib '] },
      'Santa Barbara': { barangays: [' Alibago ', ' Balingueo ', ' Banzal ', ' Botao ', ' Cablong ', ' Carusocan ', ' Dalongue ', ' Erfe ', ' Gueguesangen ', ' Leet ', ' Malanay ', ' Minien East ', ' Minien West ', ' Nilombot ', ' Patayac ', ' Payas ', ' Primicias ', ' Sapang ', ' Sonquil ', ' Tebag East ', ' Tebag West ', ' Tuliao '] },
      'Santa Maria': { barangays: [' Bali ', ' Caboluan ', ' Cal-litang ', ' Canarem ', ' Capitan ', ' Libsong ', ' Namagbagan ', ' Paitan ', ' Pugot ', ' Samon ', ' San Alejandro ', ' San Aurelio ', ' San Isidro ', ' San Vicente ', ' Santa Cruz ', ' Santo Tomas '] },
      'Santo Tomas': { barangays: [' Artacho ', ' Balaoc ', ' Barbasa ', ' Barleon ', ' Basilio ', ' Cabaluyan ', ' Carmencita ', ' La Luna ', ' Poblacion East ', ' Poblacion West ', ' Salvacion ', ' San Agustin ', ' San Antonio ', ' San Basilio ', ' San Eugenio ', ' San Isidro ', ' San Jose ', ' San Marcos ', ' Santo Tomas '] },
      'Sison': { barangays: [' Agat ', ' Alibeng ', ' Amagbagan ', ' Artacho ', ' Asan Sur ', ' Bulaoen East ', ' Bulaoen West ', ' Cabaritan ', ' Calunetan ', ' Esperanza ', ' Inmalog ', ' Lelemaan ', ' Lower Pawing ', ' Macalong ', ' Paldit ', ' Pindangan ', ' Poblacion Central ', ' Sagunto ', ' Upper Pawing '] },
      'Sual': { barangays: [' Baquioen ', ' Baybay Norte ', ' Baybay Sur ', ' Cabalitian ', ' Calumbuyan ', ' Camagsingalan ', ' Caoayan ', ' Capas ', ' Dangla ', ' Longos ', ' Pangascasan ', ' Poblacion ', ' Santo Domingo ', ' Seselangen ', ' Sioasio East ', ' Sioasio West ', ' Victoria '] },
      'Tayug': { barangays: [' Agno ', ' Amistad ', ' Anlong ', ' Aurora ', ' Banaoang ', ' Barangobong ', ' Carriedo ', ' Cayan West ', ' Diaz ', ' Magallanes ', ' Panganiban ', ' Poblacion East ', ' Poblacion West ', ' Saleng ', ' Santo Domingo '] },
      'Umingan': { barangays: ['Agapay', 'Alo-o', 'Amaronan', 'Annam', 'Antipolo', 'Cabalitian', 'Cabaruan', 'Carosalesan', 'Casilan', 'Caurdanetaan', 'Concepcion', 'Del Carmen East', 'Del Carmen West', 'Forbes', 'Lawak East', 'Lawak West', 'Lito', 'Lubong', 'Mantacdang', 'Maseil-seil', 'Nancalabasaan', 'Pangangaan', 'Poblacion East', 'Poblacion West', 'Ricos', 'San Andres', 'San Juan', 'San Leon', 'San Pablo', 'San Vicente', 'Santa Maria', 'Siblong', 'Tanggal Sawang'] },
      'Urbiztondo': { barangays: ['Angatel', 'Balangay', 'Baug', 'Bayaoas', 'Binuangan', 'Bisocol', 'Cabaruan', 'Camambugan', 'Casantiagoan', 'Catablan', 'Cayambanan', 'Galarin', 'Gueteb', 'Malayo', 'Malibong', 'Pasibi East', 'Pasibi West', ' Pisuac', 'Poblacion', 'Real', 'Salavante', 'Sawat'] },
      'Villasis': { barangays: ['Alcala', 'Amamperez', 'Apaya', 'Awai', 'Bacag', 'Barang', 'Barraca', 'Capulaan', 'Caramutan', 'La Paz', 'Labit', 'Lipay', 'Lomboy', 'Piaz', 'Puelay', 'San Blas', 'San Nicolas', 'Tombod'] },
    };

    const $preview = document.getElementById('pickupPreview');
    const $region = document.getElementById('pickup_region_group');
    const $province = document.getElementById('pickup_province');
    const $city = document.getElementById('pickup_city');
    const $barangay = document.getElementById('pickup_barangay');
    const $postal = document.getElementById('pickup_postal');
    const $detail = document.getElementById('pickup_detail');
    const $map = document.getElementById('pickup_map');

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
    const form = document.getElementById('sellerRegistrationForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const modalEl = document.getElementById('ModalCreate');

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

<script>
document.getElementById('termsModal').addEventListener('hidden.bs.modal', function () {
  // When the Terms modal closes, re-show the main registration modal
  const mainModal = document.getElementById('ModalCreate');
  const modal = new bootstrap.Modal(mainModal);
  modal.show();
});
</script>


