(function ready() {
  var input = document.getElementById('wizard-picture');
  var img = document.getElementById('wizardPicturePreview');
  if (!input || !img) return;

  function show(file) {
    if (!file) return;
    if (!/^image\//i.test(file.type)) {
      alert('Please select an image file.');
      input.value = '';
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert('Max file size is 5MB.');
      input.value = '';
      return;
    }
    var url = URL.createObjectURL(file);
    img.src = url;
    img.onload = function () {
      try {
        URL.revokeObjectURL(url);
      } catch (e) { }
    };
  }

  input.addEventListener('change', function () {
    show(this.files && this.files[0]);
  });
  img.addEventListener('click', function () {
    input && input.click();
  });
})();

document.addEventListener('turbolinks:load', function () {
  try {
    ready();
  } catch (_) { }
});
document.addEventListener('livewire:load', function () {
  try {
    ready();
  } catch (_) { }
});


(function ($) {
  function initFamilyRows() {
    const $table = $('#family-table');
    if ($table.length === 0) return;

    const MAX_ROWS = 6;
    const $tbody = $table.find('tbody');
    const $proto = $tbody.find('tr.fam-row').first();
    if ($proto.length === 0) return;

    function rowCount() {
      return $tbody.find('tr.fam-row').length;
    }

    function resetRow($tr) {
      $tr.find('input').each(function () {
        if (this.type === 'hidden') {
          this.value = ''; // keep name, clear value (family_others[])
        } else {
          this.value = '';
        }
      });
      $tr.find('select').each(function () {
        this.selectedIndex = 0; // first option is the placeholder
      });
    }

    function updateAddButtons() {
      const full = rowCount() >= MAX_ROWS;
      $tbody.find('.add-row')
        .prop('disabled', full)
        .toggleClass('disabled', full);
    }

    function addRow() {
      if (rowCount() >= MAX_ROWS) return;
      const $clone = $proto.clone(false, false);
      resetRow($clone);
      $tbody.append($clone);
      updateAddButtons();
    }

    function removeRow(btn) {
      if (rowCount() <= 1) return; // keep at least one row
      $(btn).closest('tr.fam-row').remove();
      updateAddButtons();
    }

    // Prevent duplicate bindings if this runs multiple times
    $tbody.off('.familyRows');

    // Delegate clicks to tbody (works for new rows)
    $tbody.on('click.familyRows', '.add-row', function (e) {
      e.preventDefault();
      addRow();
    });

    $tbody.on('click.familyRows', '.remove-row', function (e) {
      e.preventDefault();
      removeRow(this);
    });

    updateAddButtons();
  }

  // Run once DOM is ready
  $(initFamilyRows);
  // If you use Turbolinks or Livewire, also run on their events:
  document.addEventListener('turbolinks:load', initFamilyRows);
  document.addEventListener('livewire:load', initFamilyRows);
})(jQuery);


document.addEventListener('DOMContentLoaded', function () {
  var sel = document.getElementById('barangay_id');
  var wrap = document.getElementById('barangay_other_wrap');
  function toggleOther() {
    if (!sel || !wrap) return;
    wrap.style.display = (sel.value === 'other') ? '' : 'none';
  }
  toggleOther();
  if (window.jQuery) {
    $(document).on('change', '#barangay_id', toggleOther);
    $(document).on('select2:select', '#barangay_id', toggleOther);
  } else {
    sel.addEventListener('change', toggleOther);
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form');
  const sel = document.getElementById('barangay_id');
  if (!form || !sel) return;

  form.addEventListener('submit', function () {
    if (sel.value === 'other') sel.value = ''; // becomes null server-side
  });
});
