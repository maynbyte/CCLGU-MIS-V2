<!-- Print Payout Modal -->
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
        <div class="form-group">
          <label>Types of Assistance</label>
          <div class="d-flex flex-wrap gap-2">
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_financial" value="Financial Assistance">
              <label class="form-check-label" for="type_financial">Financial Assistance</label>
            </div>
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_guarantee" value="Guarantee Letter">
              <label class="form-check-label" for="type_guarantee">Guarantee Letter</label>
            </div>
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_burial" value="Burial Assistance">
              <label class="form-check-label" for="type_burial">Burial Assistance</label>
            </div>
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_medical" value="Medical Assistance">
              <label class="form-check-label" for="type_medical">Medical Assistance</label>
            </div>
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_education" value="Education Assistance">
              <label class="form-check-label" for="type_education">Education Assistance</label>
            </div>
            <div class="form-check form-check-inline mr-3">
              <input class="form-check-input assistance-type-checkbox" type="checkbox" id="type_solicitation" value="Solicitation">
              <label class="form-check-label" for="type_solicitation">Solicitation</label>
            </div>
          </div>
          <small class="form-text text-muted">Select which types of assistance to include in the payout report.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="printPayoutBtn" class="btn btn-primary"><i class="fas fa-print"></i> Preview & Print</button>
      </div>
    </div>
  </div>
</div>
