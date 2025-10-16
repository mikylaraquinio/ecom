<x-app-layout>

<style>
    [x-cloak] { display: none !important; }
</style>

    <div class="max-w-7xl mx-auto py-8 px-6">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Admin Dashboard</h1>

        <div x-data="{ mainTab: 'sellers' }">
            
            <!-- === ICON NAV BAR === -->
            <div class="bg-white border rounded-lg shadow-sm p-4 mb-6">
                <div class="flex gap-4 overflow-x-auto pb-2 justify-start">
                    <a href="#" 
                       @click="mainTab = 'sellers'" 
                       class="flex flex-col items-center justify-center border rounded-lg px-4 py-3 w-[130px] flex-shrink-0 text-center text-gray-700 hover:bg-blue-50 transition"
                       :class="mainTab === 'sellers' ? 'ring-2 ring-blue-500' : ''">
                        <i class="fas fa-user-check text-xl mb-1"></i>
                        <span class="text-sm font-semibold">Seller Requests</span>
                    </a>

                    <!-- Reports -->
                    <a href="#" 
                    @click="mainTab = 'reports'" 
                    class="flex flex-col items-center justify-center border rounded-lg px-4 py-3 w-[130px] flex-shrink-0 text-center text-gray-700 hover:bg-green-50 transition"
                    :class="mainTab === 'reports' ? 'ring-2 ring-green-500' : ''">
                        <i class="fas fa-file-alt text-xl mb-1"></i>
                        <span class="text-sm font-semibold">Reports</span>
                    </a>
                </div>
            </div>

            <!-- === SELLER REQUESTS TABLE === -->
            <div x-show="mainTab === 'sellers'" class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Seller Applications</h2>

                @php
                    $sellers = \App\Models\Seller::with('user')->orderByRaw("
                        CASE 
                            WHEN status = 'pending' THEN 1 
                            WHEN status = 'approved' THEN 2 
                            WHEN status = 'rejected' THEN 3 
                        END
                    ")->get();
                @endphp

                @if($sellers->isEmpty())
                    <p class="text-gray-500">No seller applications found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-left text-gray-700">
                                <tr>
                                    <th class="px-4 py-2 border">ID</th>
                                    <th class="px-4 py-2 border">Shop Name</th>
                                    <th class="px-4 py-2 border">Full Name</th>
                                    <th class="px-4 py-2 border">Phone</th>
                                    <th class="px-4 py-2 border">Region</th>
                                    <th class="px-4 py-2 border">Province</th>
                                    <th class="px-4 py-2 border">City</th>
                                    <th class="px-4 py-2 border">Barangay</th>
                                    <th class="px-4 py-2 border">Business Type</th>
                                    <th class="px-4 py-2 border text-center">Status</th>
                                    <th class="px-4 py-2 border text-center">Documents</th>
                                    <th class="px-4 py-2 border text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellers as $seller)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $seller->id }}</td>
                                        <td class="px-4 py-2 border font-medium">{{ $seller->shop_name }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_full_name }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_phone }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_region_group }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_province }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_city }}</td>
                                        <td class="px-4 py-2 border">{{ $seller->pickup_barangay }}</td>
                                        <td class="px-4 py-2 border">{{ ucfirst($seller->business_type) }}</td>

                                        <td class="px-4 py-2 border text-center">
                                            @if($seller->status === 'pending')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Pending</span>
                                            @elseif($seller->status === 'approved')
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Approved</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Rejected</span>
                                            @endif
                                        </td>

                                        <!-- Documents Column -->
                                        <td class="px-4 py-2 border text-center">
                                            <button 
                                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewDocsModal"
                                                onclick="
                                                    document.getElementById('sellerName').innerText = '{{ $seller->shop_name }}';
                                                    document.getElementById('sellerGovID').src = '{{ asset('storage/' . $seller->gov_id_path) }}';
                                                    document.getElementById('sellerRSBSA').src = '{{ asset('storage/' . $seller->rsbsa_path) }}';
                                                    document.getElementById('sellerMayor').src = '{{ asset('storage/' . $seller->mayors_permit_path) }}';
                                                "
                                            >
                                                View Documents
                                            </button>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-2 border text-center">
                                            @if($seller->status === 'pending')
                                                <div class="flex justify-center space-x-2">
                                                    <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.sellers.deny', $seller->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">
                                                            Deny
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($seller->status === 'approved')
                                                <button disabled class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs cursor-not-allowed">Approved</button>
                                            @else
                                                <button disabled class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs cursor-not-allowed">Rejected</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div x-show="mainTab === 'reports'" x-cloak>
            <div class="bg-white border rounded-lg shadow-sm p-4">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">User Reports</h2>

                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Reporter</th>
                            <th>Target Type</th>
                            <th>Target Name</th>
                            <th>Category</th>
                            <th>Severity</th>
                            <th>Description</th>
                            <th>Contact Email</th>
                            <th>Attachment</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>Date Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            @php
                                $targetName = 'N/A';
                                if($report->target_type === 'Product') {
                                    $product = \App\Models\Product::find($report->target_id);
                                    $targetName = $product ? $product->name : 'Deleted Product';
                                } elseif($report->target_type === 'User') {
                                    $user = \App\Models\User::find($report->target_id);
                                    $targetName = $user ? $user->name : 'Deleted User';
                                }
                            @endphp
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->user?->name ?? 'Deleted User' }}</td>
                                <td>{{ $report->target_type ?? 'General' }}</td>
                                <td>{{ $targetName }}</td>
                                <td>{{ $report->category }}</td>
                                <td>{{ $report->severity ?? 'N/A' }}</td>
                                <td>{{ $report->description }}</td>
                                <td>{{ $report->contact_email ?? '-' }}</td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#attachmentModal" 
                                        onclick="document.getElementById('attachmentModalImg').src='{{ asset('storage/'.$report->attachment) }}';"
                                    >
                                        View
                                    </button>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($report->status) {
                                            'Pending' => 'badge bg-warning text-dark',
                                            'Resolved' => 'badge bg-success',
                                            'Rejected' => 'badge bg-danger',
                                            default => 'badge bg-secondary'
                                        };
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ $report->status }}</span>
                                </td>
                                <td>
                                    @if(strtolower($report->status) === 'pending')
                                        <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="d-flex gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="Reviewed" class="btn btn-warning btn-sm px-3 py-1 text-sm">reviewed</button>
                                            <button type="submit" name="status" value="Resolved" class="btn btn-success btn-sm px-3 py-1 text-sm">resolved</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 text-sm">-</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bootstrap Modal for Report Attachment -->
            <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-3 shadow">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="attachmentModalLabel">
                                <i class="fas fa-paperclip me-2"></i> Attachment Preview
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            <img id="attachmentModalImg" src="" class="img-fluid rounded border shadow-sm" alt="Attachment Preview">
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @push('scripts')
        <script>
            // Bootstrap modal dynamic image loading
            var attachmentModal = document.getElementById('attachmentModal')
            attachmentModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget
                var src = button.getAttribute('data-src')
                var modalImg = attachmentModal.querySelector('#attachmentModalImg')
                modalImg.src = src
            })
        </script>
        @endpush

            <!-- === DOCUMENT VIEWER MODAL (Bootstrap style) === -->
            <div class="modal fade" id="viewDocsModal" tabindex="-1" aria-labelledby="viewDocsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-3 shadow">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-bold" id="viewDocsModalLabel">
                                    <i class="fas fa-file-alt me-2"></i><span id="sellerName"></span> Documents
                                </h5>
                                <div class="text-muted small">Seller verification files</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="space-y-4">
                                <div class="mb-3">
                                    <p class="fw-semibold text-gray-700 mb-2">Government ID:</p>
                                    <img id="sellerGovID" src="" alt="Gov ID" class="img-fluid rounded border shadow-sm">
                                </div>
                                <div class="mb-3">
                                    <p class="fw-semibold text-gray-700 mb-2">RSBSA:</p>
                                    <img id="sellerRSBSA" src="" alt="RSBSA" class="img-fluid rounded border shadow-sm">
                                </div>
                                <div class="mb-3">
                                    <p class="fw-semibold text-gray-700 mb-2">Mayor’s Permit:</p>
                                    <img id="sellerMayor" src="" alt="Mayor’s Permit" class="img-fluid rounded border shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

</x-app-layout>
