$(document).ready(function() {
  // Initialize Select2
  const $select = $("#recipientEmails").select2({
      placeholder: "Select recipients",
      allowClear: true,
      templateResult: formatOption
  });

  // Store the original HTML of select
  const originalHtml = $("#recipientEmails").html();

  // Function to format options and handle visibility
  function formatOption(option) {
      if (!option.id) return option.text; // Skip placeholder
      
      const currentSelections = $select.val() || [];
      if (currentSelections.length > 0) {
          const firstSelection = currentSelections[0];
          
          // If "All Faculty" is selected, only show "All Faculty" option
          if (firstSelection === 'all-faculty') {
              if (option.id !== 'all-faculty') return null;
          }
          // If a department is selected, only show department options
          else if (firstSelection.startsWith('department-')) {
              if (!option.id.startsWith('department-')) return null;
          }
          // If an email is selected, only show email options
          else {
              if (option.id === 'all-faculty' || option.id.startsWith('department-')) return null;
          }
      }
      
      return option.text;
  }

  // Handle selection changes
  $select.on('select2:select', function(e) {
      const currentValue = e.params.data.id;
      
      // Clear previous selections
      $select.val(null).trigger('change');
      
      // Set new selection
      $select.val([currentValue]).trigger('change');
  });

  // Handle clearing selection
  $select.on('select2:clear', function(e) {
      // Reset the select to original state
      $("#recipientEmails").html(originalHtml);
      $select.trigger('change');
  });

  // Add custom styles
  $('<style>')
      .text(`
          .select2-container--default .select2-results__group {
              background-color: #f8f9fa;
              padding: 6px;
              font-weight: bold;
          }
          .select2-container--default .select2-results__option {
              padding-left: 15px;
          }
          .select2-container--default .select2-results__option[aria-selected=true] {
              background-color: #e9ecef;
          }
          .select2-container--default .select2-selection--multiple {
              min-height: 38px;
          }
      `)
      .appendTo('head');
});