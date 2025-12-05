<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" role="dialog" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkEditModalLabel">Edit Selected – Latest Financial Assistance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="bulk_scheduled_fa">Payout Schedule</label>
          <input type="date" id="bulk_scheduled_fa" class="form-control">
          <small class="form-text text-muted">Leave blank to keep existing schedule. If Status = Claimed, schedule will be cleared.</small>
        </div>
        <div class="form-group">
          <label for="bulk_status">Status</label>
          <select id="bulk_status" class="form-control">
            <option value="">— No change —</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Pending">Pending</option>
            <option value="Claimed">Claimed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="bulkApplyBtn" class="btn btn-primary">Apply Changes</button>
      </div>
    </div>
  </div>
</div>
