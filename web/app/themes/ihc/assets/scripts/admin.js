/*
 * IL Humanities admin - Firebelly 2015
*/

// Good design for good reason for good namespace
var IHC_admin = (function($) {

  var _uploadFiles,
      _uploadCount = 0,
      _uploadedEntries = 0,
      _uploadTime = 0;

  function _init() {
    // AJAX CSV import handling
    if ($('body[class*="csv-importer"]').length) {
      if (window.File && window.FileList && window.FileReader) {
        $('#csv-submit').prop('disabled', true);

        // Init drag-and-drop dropzone if supported
        var filedrag = $('#filedrag')[0];
        filedrag.addEventListener('dragover', IHC_admin.dragHover, false);
        filedrag.addEventListener('dragleave', IHC_admin.dragHover, false);
        filedrag.addEventListener('drop', IHC_admin.dragDrop, false);
        filedrag.style.display = 'block';

        // If selecting files using input, update drag-drop zone with number of files
        $('#csv-import').on('change', function() {
          _uploadFiles = $('#csv-import')[0].files;
          _updateFileDrag();
        });

        // Multifile CSV uploads with progress bar
        $('#csv-upload-form').on('submit', function(e) {
          // Avoid standard submit
          e.preventDefault();

          // Hide any previous notices
          $('#csv-upload-form .wrap').slideUp();

          // Reset stats
          _uploadCount = _uploadedEntries = _uploadTime = 0;

          // Extract HTML5 multifile input value
          // _uploadFiles = $('#csv-import')[0].files;

          if (_uploadFiles && _uploadFiles.length > 0) {
            // Submit uploads and process CSV files
            _handleUploadFile();
          }
        });
      }
    }
  }

  function _dragHover(e) {
    e.stopPropagation();
    e.preventDefault();
    e.target.className = (e.type === "dragover" ? "hover" : "");
  }
  function _dragDrop(e) {
    _dragHover(e);
    _uploadFiles = e.target.files || e.dataTransfer.files;
    _updateFileDrag();
  }
  function _updateFileDrag() {
    $('#filedrag').text((_uploadFiles.length > 0) ? _uploadFiles.length + ' files to import' : 'or drop files here');
    $('#csv-submit').prop('disabled', (_uploadFiles.length===0));
  }

  function _handleUploadFile() {
    // Disable form elements to avoid interrupting upload
    $('#csv-upload-form input').prop('disabled', true);

    // Build FormData object to submit with each file
    var data = new FormData();
    $('#csv-upload-form input[type=submit]').val('Uploading...');
    data.append('csv_import[]', _uploadFiles[_uploadCount]);

    // This sets action to trigger AJAX function
    data.append('action', $('#csv-upload-form input[name=action]').val());

    // Show progress bar and set to percentage width
    $('.progress-bar').show().addClass('active');
    $('.progress-done').css('width', Math.floor((_uploadCount+1) / _uploadFiles.length * 100) + '%');
    $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          _uploadCount++;
          // update stats if upload was successful
          if (typeof data.stats !== 'undefined') {
            _uploadedEntries += parseInt(data.stats.entries);
            _uploadTime += parseFloat(data.stats.exec_time);
          }
          // If error, show and stop uploading
          if (typeof data.error !== 'undefined') {
            $('<div class="wrap"><div class="error"><p>' + data.error[0] + '</p></div></div>').insertAfter('.progress-bar');
            _resetCSVUploadForm();
          } else if (_uploadCount < _uploadFiles.length) {
            // Otherwise, move onto processing next file
            _handleUploadFile();
          } else {
            // If we're all done, hide progress bar, show stats, and reset the form
            $('.progress-bar').fadeOut('slow').removeClass('active');
            $('<div class="wrap"><div class="updated">Success: ' + data.notice.join(' and ') + ' in ' + _uploadTime.toFixed(2) + ' seconds.</div></div>').insertBefore('#csv-upload-form fieldset');
            _resetCSVUploadForm();
          }
        },
    });
  }

  // Resets form after uploads
  function _resetCSVUploadForm() {
    $('#csv-upload-form input').prop('disabled', false).val('');
    $('#csv-upload-form input[type=submit]').val('Import');
    _uploadFiles = [];
    _updateFileDrag();
  }

  // public functions
  return {
    init: _init,
    dragHover: _dragHover,
    dragDrop: _dragDrop
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(IHC_admin.init);
