<!-- Send SMS Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="sendSmsModalLabel"><i class="fas fa-sms mr-2"></i>Send SMS to Selected</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          Sending to <strong id="smsRecipientCount">0</strong> recipient(s) out of <strong id="smsTotalSelected">0</strong> selected.
          <span class="d-block small mt-1">Only records with valid phone numbers will receive the message.</span>
        </div>
        
        <div class="form-group">
          <label for="smsMessageText">Message <span class="text-danger">*</span></label>
          <textarea 
            id="smsMessageText" 
            class="form-control" 
            rows="4" 
            maxlength="160" 
            placeholder="Type your message here (max 160 characters)..."
            required></textarea>
          <small class="form-text text-muted">
            <span id="smsCharCount">0/160</span> characters
          </small>
        </div>

        <div class="form-group">
          <label class="d-block">Quick Templates:</label>
          <button type="button" class="btn btn-sm btn-outline-secondary sms-template" data-message="Your financial assistance is ready for claim at CCLGU office.">Claim Ready</button>
          <button type="button" class="btn btn-sm btn-outline-secondary sms-template" data-template="payout-reminder">Payout Reminder</button>
          <button type="button" class="btn btn-sm btn-outline-secondary sms-template" data-message="Your application has been processed. You will be notified once approved.">Application Update</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="sendSmsBtn" class="btn btn-info"><i class="fas fa-paper-plane"></i> Send SMS</button>
      </div>
    </div>
  </div>
</div>
