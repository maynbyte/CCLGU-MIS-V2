<!-- Print Payout Modal (extracted to partial) -->
<div class="modal fade" id="printPayoutModal" tabindex="-1" role="dialog" aria-labelledby="printPayoutModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printPayoutModalLabel">Select Payout Date</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="print_payout_date">Payout Date</label>
          <input type="date" id="print_payout_date" class="form-control" required>
          <small class="form-text text-muted">Select the scheduled payout date to print records.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="printPayoutBtn" class="btn btn-primary"><i class="fas fa-print"></i> Preview & Print</button>
      </div>
    </div>
  </div>
</div>
