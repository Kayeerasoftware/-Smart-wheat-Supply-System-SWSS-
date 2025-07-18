// Vendor Management JavaScript Functions

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // Search and filter functionality
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function(e) {
            const status = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(3)');
                if (statusCell) {
                    const statusText = statusCell.textContent.toLowerCase();
                    row.style.display = !status || statusText.includes(status) ? '' : 'none';
                }
            });
        });
    }
});

// Vendor modal functions
function viewVendor(vendorId) {
    console.log('Opening vendor details for ID:', vendorId);
    
    // Show loading state
    document.getElementById('vendorModal').classList.remove('hidden');
    document.getElementById('vendorModalContent').innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div></div>';
    
    // Fetch vendor details
    const url = `/admin/vendors/${vendorId}/details`;
    console.log('Fetching from URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                displayVendorDetails(data.vendor);
            } else {
                document.getElementById('vendorModalContent').innerHTML = '<div class="text-red-400 text-center py-8">Error loading vendor details</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('vendorModalContent').innerHTML = `<div class="text-red-400 text-center py-8">Error loading vendor details: ${error.message}</div>`;
        });
}

function displayVendorDetails(vendor) {
    // Business Images Section
    let businessImagesHtml = '';
    if (vendor.business_images && Object.keys(vendor.business_images).length > 0) {
        businessImagesHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">Business Images</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${Object.entries(vendor.business_images).map(([type, url]) => `
                        <div class="bg-gray-800 rounded-lg p-3">
                            <div class="text-sm text-gray-400 mb-2 capitalize">${type.replace('_', ' ')}</div>
                            <img src="${url}" alt="${type}" class="w-full h-32 object-cover rounded-lg border border-gray-600 hover:scale-105 transition-transform cursor-pointer" onclick="openImageModal('${url}', '${type}')">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    // Facility Visits Section
    let facilityVisitsHtml = '';
    if (vendor.facility_visits && vendor.facility_visits.length > 0) {
        facilityVisitsHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">Facility Visits</h4>
                <div class="space-y-3">
                    ${vendor.facility_visits.map(visit => `
                        <div class="bg-gray-800 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm text-gray-400">${new Date(visit.scheduled_at).toLocaleDateString()}</div>
                                <span class="px-2 py-1 rounded-full text-xs ${visit.status === 'completed' ? 'bg-green-500/20 text-green-300' : visit.status === 'scheduled' ? 'bg-blue-500/20 text-blue-300' : 'bg-yellow-500/20 text-yellow-300'}">
                                    ${visit.status.charAt(0).toUpperCase() + visit.status.slice(1)}
                                </span>
                            </div>
                            ${visit.notes ? `<p class="text-sm text-gray-300">${visit.notes}</p>` : ''}
                            ${visit.outcome ? `<p class="text-sm text-gray-400 mt-1">Outcome: ${visit.outcome}</p>` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    // Scores Section
    let scoresHtml = '';
    if (vendor.scores && vendor.scores.total) {
        scoresHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">Assessment Scores</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-400">${vendor.scores.financial || 0}</div>
                        <div class="text-sm text-gray-400">Financial</div>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-green-400">${vendor.scores.reputation || 0}</div>
                        <div class="text-sm text-gray-400">Reputation</div>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-purple-400">${vendor.scores.compliance || 0}</div>
                        <div class="text-sm text-gray-400">Compliance</div>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-yellow-400">${vendor.scores.total || 0}</div>
                        <div class="text-sm text-gray-400">Total</div>
                    </div>
                </div>
            </div>
        `;
    }
    // Only display fields that are not N/A or empty
    let businessInfoHtml = '';
    if (vendor.application_data?.business_name && vendor.application_data.business_name !== 'N/A') {
        businessInfoHtml += `<div><label class=\"text-sm text-gray-400\">Business Name</label><p class=\"text-white font-medium\">${vendor.application_data.business_name}</p></div>`;
    }
    let contactInfoHtml = '';
    if (vendor.user?.email && vendor.user.email !== 'N/A') {
        contactInfoHtml += `<div><label class=\"text-sm text-gray-400\">Email</label><p class=\"text-white\">${vendor.user.email}</p></div>`;
    }
    const content = `
        ${businessImagesHtml}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Business Information</h4>
                <div class="space-y-3">
                    ${businessInfoHtml || '<p class=\"text-gray-400\">No business information available.</p>'}
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Contact Information</h4>
                <div class="space-y-3">
                    ${contactInfoHtml || '<p class=\"text-gray-400\">No contact information available.</p>'}
                </div>
            </div>
        </div>
        ${scoresHtml}
        ${facilityVisitsHtml}
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-white mb-4">Additional Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="text-sm text-gray-400">Processing Status</label><p class="text-white">${vendor.processing_status || 'N/A'}</p></div>
                <div><label class="text-sm text-gray-400">Application Date</label><p class="text-white">${new Date(vendor.created_at).toLocaleDateString()}</p></div>
            </div>
        </div>
        <div class="mt-6">
            <h4 class="text-lg font-semibold text-white mb-4">Scores</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-card p-4 rounded-xl border border-gray-700/50"><div class="text-center"><p class="text-sm text-white">Financial Score</p><p class="text-2xl font-bold text-white">${vendor.score_financial || 0}%</p></div></div>
                <div class="glass-card p-4 rounded-xl border border-gray-700/50"><div class="text-center"><p class="text-sm text-white">Reputation Score</p><p class="text-2xl font-bold text-white">${vendor.score_reputation || 0}%</p></div></div>
                <div class="glass-card p-4 rounded-xl border border-gray-700/50"><div class="text-center"><p class="text-sm text-white">Compliance Score</p><p class="text-2xl font-bold text-white">${vendor.score_compliance || 0}%</p></div></div>
            </div>
        </div>
    `;
    document.getElementById('vendorModalContent').innerHTML = content;
}

function closeVendorModal() {
    document.getElementById('vendorModal').classList.add('hidden');
}

function openImageModal(imageUrl, imageType) {
    document.getElementById('imageModalImg').src = imageUrl;
    document.getElementById('imageModalTitle').textContent = imageType.replace('_', ' ').toUpperCase();
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Vendor approval functions
function approveVendor(vendorId) {
    if (confirm('Are you sure you want to approve this vendor?')) {
        fetch(`/admin/vendors/${vendorId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Vendor approved successfully!');
                location.reload();
            } else {
                alert('Error approving vendor: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error approving vendor:', error);
            alert('Error approving vendor: ' + error.message);
        });
    }
}

function rejectVendor(vendorId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        fetch(`/admin/vendors/${vendorId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting vendor');
            }
        });
    }
}

function scheduleVisit(vendorId) {
    // Show visit scheduling modal
    showVisitModal(vendorId);
}

function showVisitModal(vendorId) {
    const modal = document.createElement('div');
    modal.id = 'visitModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="glass-card rounded-2xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Schedule Facility Visit</h3>
                    <button onclick="closeVisitModal()" class="text-white hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="visitForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Visit Date & Time <span class="text-red-400">*</span></label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" required
                               class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="${new Date().toISOString().slice(0, 16)}">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add any notes about the visit..."></textarea>
                    </div>
                    <div id="errorMessage" class="hidden mb-4 p-3 bg-red-500/20 border border-red-500/50 text-red-200 rounded-lg"></div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVisitModal()" 
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Schedule Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('visitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitVisitSchedule(vendorId);
    });
}

function closeVisitModal() {
    const modal = document.getElementById('visitModal');
    if (modal) {
        modal.remove();
    }
}

function submitVisitSchedule(vendorId) {
    let scheduledAt = document.getElementById('scheduled_at').value;
    const notes = document.getElementById('notes').value;
    const errorDiv = document.getElementById('errorMessage');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!scheduledAt) {
        showError('Please select a date and time for the visit.');
        return;
    }

    // Convert from 'YYYY-MM-DDTHH:mm' to 'YYYY-MM-DD HH:mm:00' for Laravel
    if (scheduledAt.includes('T')) {
        scheduledAt = scheduledAt.replace('T', ' ') + ':00';
    }

    // Debug: log the value being sent
    console.log('Scheduled At:', scheduledAt);
    
    // Show loading state
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Scheduling...';
    submitBtn.disabled = true;
    hideError();
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/vendors/${vendorId}/schedule-visit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            scheduled_at: scheduledAt,
            notes: notes
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if response is ok
        if (!response.ok) {
            // Try to get the response text for debugging
            return response.text().then(text => {
                console.log('Response text:', text);
                throw new Error(`HTTP ${response.status}: ${response.statusText}. Response: ${text.substring(0, 200)}`);
            });
        }
        
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.log('Non-JSON response:', text);
                throw new Error(`Expected JSON but got ${contentType}. Response: ${text.substring(0, 200)}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            closeVisitModal();
            showSuccess(data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error scheduling visit:', error);
        showError('Error: ' + error.message);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

function hideError() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
    }
}

function showSuccess(message) {
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

function completeVisit(vendorId) {
    if (confirm('Are you sure you want to mark this visit as completed?')) {
        fetch(`/admin/vendors/${vendorId}/complete-visit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Visit completed successfully!');
                location.reload();
            } else {
                alert('Error completing visit: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error completing visit: ' + error.message);
        });
    }
}

function checkVisits() {
    if (confirm('Are you sure you want to manually check and auto-complete overdue visits?')) {
        fetch('/admin/vendors/check-visits', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error checking overdue visits: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error checking overdue visits: ' + error.message);
        });
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const vendorModal = document.getElementById('vendorModal');
    const imageModal = document.getElementById('imageModal');
    
    if (event.target === vendorModal) {
        closeVendorModal();
    }
    
    if (event.target === imageModal) {
        closeImageModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVendorModal();
        closeImageModal();
    }
});

// Make functions globally available
window.viewVendor = viewVendor;
window.approveVendor = approveVendor;
window.rejectVendor = rejectVendor;
window.scheduleVisit = scheduleVisit;
window.completeVisit = completeVisit;
window.checkVisits = checkVisits;
window.closeVendorModal = closeVendorModal;
window.openImageModal = openImageModal;
window.closeImageModal = closeImageModal;
window.closeVisitModal = closeVisitModal; 