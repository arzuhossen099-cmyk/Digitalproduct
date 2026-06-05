<?php
$title = "Search Leads";
$active_page = "search";
require_once __DIR__ . '/includes/header_user.php';
?>

<div class="row mb-4">
    <div class="col-lg-3">
        <div class="lp-card sticky-top" style="top: 100px;">
            <h6 class="fw-bold mb-4 text-accent"><i class="fas fa-sliders-h me-2"></i> Advanced Filters</h6>
            <form id="searchForm">
                <div class="mb-3">
                    <label class="form-label text-muted small">Industry</label>
                    <input type="text" name="industry" class="lp-input w-100" placeholder="e.g. Technology">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Job Title</label>
                    <input type="text" name="job_title" class="lp-input w-100" placeholder="e.g. CEO">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Company</label>
                    <input type="text" name="company" class="lp-input w-100" placeholder="e.g. Google">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Location</label>
                    <input type="text" name="country" class="lp-input w-100" placeholder="e.g. USA">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Seniority</label>
                    <select name="seniority" class="lp-input w-100">
                        <option value="">Any</option>
                        <option value="Entry">Entry</option>
                        <option value="Senior">Senior</option>
                        <option value="Director">Director</option>
                        <option value="VP">VP</option>
                        <option value="C-Level">C-Level</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Technology</label>
                    <input type="text" name="technology_stack" class="lp-input w-100" placeholder="e.g. React, PHP">
                </div>
                <button type="submit" class="btn-lp-primary w-100 mt-2">Apply Filters</button>
                <button type="reset" class="btn-lp-outline w-100 mt-2">Clear Filters</button>
            </form>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" id="totalCount">Total Leads: <span class="skeleton d-inline-block" style="width: 100px; height: 25px;"></span></h4>
            <div class="btn-group">
                <button class="btn btn-lp-outline btn-sm"><i class="fas fa-file-export me-1"></i> Export</button>
                <button class="btn btn-lp-outline btn-sm"><i class="fas fa-save me-1"></i> Save Search</button>
            </div>
        </div>

        <div class="lp-table-container">
            <div class="table-responsive">
                <table class="lp-table" id="leadsTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="form-check-input bg-dark border-secondary"></th>
                            <th>Lead Name</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="resultsContainer">
                        <!-- Skeletons -->
                        <?php for($i=0; $i<5; $i++): ?>
                        <tr>
                            <td><div class="skeleton" style="width: 15px; height: 15px;"></div></td>
                            <td><div class="skeleton" style="width: 120px; height: 20px;"></div></td>
                            <td><div class="skeleton" style="width: 100px; height: 20px;"></div></td>
                            <td><div class="skeleton" style="width: 80px; height: 20px;"></div></td>
                            <td><div class="skeleton" style="width: 60px; height: 20px;"></div></td>
                            <td><div class="skeleton" style="width: 40px; height: 20px;"></div></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <nav id="paginationContainer" class="mt-4"></nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const resultsContainer = document.getElementById('resultsContainer');
    const totalCount = document.getElementById('totalCount');

    function fetchLeads(page = 1) {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        params.append('page', page);

        fetch('api/search_leads.php?' + params.toString())
            .then(response => response.json())
            .then(data => {
                totalCount.innerHTML = `Total Leads: <span class="text-accent">${data.total.toLocaleString()}</span>`;
                renderLeads(data.leads);
                renderPagination(data.pages, data.current_page);
            });
    }

    function renderLeads(leads) {
        if (leads.length === 0) {
            resultsContainer.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No matching leads found.</td></tr>';
            return;
        }

        let html = '';
        leads.forEach(lead => {
            html += `
                <tr>
                    <td data-label="Select"><input type="checkbox" class="form-check-input bg-dark border-secondary"></td>
                    <td data-label="Lead Name">
                        <div class="fw-bold">${lead.full_name}</div>
                        <div class="text-muted small">${lead.email || lead.email_masked}</div>
                    </td>
                    <td data-label="Job Title">${lead.job_title}</td>
                    <td data-label="Company"><span class="text-accent">${lead.company_name}</span></td>
                    <td data-label="Location">${lead.country || 'N/A'}</td>
                    <td data-label="Action">
                        ${lead.revealed ?
                            '<span class="lp-badge lp-badge-success">Revealed</span>' :
                            `<button class="btn btn-lp-primary btn-sm px-3" onclick="revealLead(${lead.id})">Reveal</button>`
                        }
                    </td>
                </tr>
            `;
        });
        resultsContainer.innerHTML = html;
    }

    function renderPagination(totalPages, currentPage) {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        let html = '<ul class="pagination justify-content-center">';
        for (let i = 1; i <= Math.min(5, totalPages); i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link bg-secondary border-secondary text-primary" href="#" onclick="event.preventDefault(); window.fetchLeads(${i})">${i}</a></li>`;
        }
        html += '</ul>';
        paginationContainer.innerHTML = html;
    }

    window.fetchLeads = fetchLeads;
    searchForm.addEventListener('submit', (e) => { e.preventDefault(); fetchLeads(1); });
    setTimeout(() => fetchLeads(1), 500); // Delay for skeleton demo
});

function revealLead(id) {
    if (confirm('Revealing this lead will spend credits. Continue?')) {
        const formData = new FormData();
        formData.append('lead_id', id);

        fetch('api/reveal_lead.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Lead revealed! Email: ' + data.lead.email);
                window.fetchLeads();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
