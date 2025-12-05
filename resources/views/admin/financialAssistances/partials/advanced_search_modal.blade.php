<!-- Advanced Search Modal -->
<div class="modal fade" id="advancedSearchModal" tabindex="-1" role="dialog" aria-labelledby="advancedSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl ml-auto" role="document">
    <div class="modal-content">
      <div class="modal-header p-1 align-items-start" role="banner">
        <div class="d-flex align-items-start">
          <div class="mr-3 mt-1 text-primary"><i class="fas fa-search fa-lg"></i></div>
          <div>
            <h5 class="modal-title mb-1" id="advancedSearchModalLabel">Advanced Search</h5>
            <div class="text-muted small">Search and filter Financial Assistance records with advanced options.</div>
          </div>
        </div>
        <div class="ml-auto d-flex align-items-center">
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped table-hover" id="advancedSearchTable" style="width: 100%;">
          <thead>
            <tr>
              <th>ID</th>
              <th>Settings</th>
              <th>Claimant Name</th>
              <th>Patient Name</th>
              <th>Assistance Type</th>
              <th>Date Claimed</th>
              <th>Status</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<style>

          /* When sidebar is open (AdminLTE: not .sidebar-collapse), offset dialog a bit from the sidebar */
          body:not(.sidebar-collapse) #advancedSearchModal .modal-dialog {
            /* AdminLTE sidebar width is typically ~250px */
            margin-left: 260px; /* shift dialog to the right of sidebar */
            margin-right: 1rem; /* keep slight right spacing */
          }

          /* When sidebar is collapsed, center the modal */
          body.sidebar-collapse #advancedSearchModal .modal-dialog {
            margin-left: auto; /* center horizontally by default */
            margin-right: auto;
          }
  /* Increase width and shift the dialog to the right */
  #advancedSearchModal .modal-dialog {
    max-width: 1025px;
    margin-left: auto;
  }
  #advancedSearchModal .modal-body {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }
  /* Header styling for Advanced Search modal */
  #advancedSearchModal .modal-header {
    background: #fbfbfc;
    border-bottom: 1px solid #e9ecef;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
  }
  #advancedSearchModal .modal-title {
    font-weight: 600;
    font-size: 1.05rem;
  }
  #advancedSearchModal .modal-header .text-muted.small {
    font-size: 0.82rem;
  }
  #advancedSearchModal #advResetFilters {
    min-width: 86px;
  }
</style>
