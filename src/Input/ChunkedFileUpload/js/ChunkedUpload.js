initChunkedUpload = (
  input_id,
  form_id,
  url_fetch_upload_url,
  chunk_size,
  after_upload_callback,
  url_upload_aborted,
  url_upload_failed,
  url_session_refresh,
  submit_cmd
) => {
  let $submit_btn = $('input[name="cmd[' + submit_cmd + ']"]');
  $submit_btn.prop("disabled", !!submit_cmd);
  let $input = $('#' + input_id);
  let $filename_input = $('#' + input_id + '_filename');
  $input.on('change', () => {
    let file = $input.get(0).files[0];
    $filename_input.val(file ? file.name : '');
    $submit_btn.prop("disabled", (!file && !!submit_cmd));
  });
  $submit_btn.on('click', (event) => {
    let file = $input.get(0).files[0];
    event.preventDefault();
    il.waiter.show();
    this.file_in_progress = file;

    $input.fileupload();
    // fetch upload url
    $.ajax({
      type: 'post',
      url: url_fetch_upload_url,
      data: { filename: file.name }
    }).success((response) => {
      response = JSON.parse(response);
      // callback functions
      $input.on('fileuploaddone', (e, data) => {
        clearInterval(this.session_refresher);
        il.waiter.hide();
        this.file_in_progress = undefined;
        if (typeof after_upload_callback !== 'undefined') {
          executeFunctionByName(after_upload_callback, window, file);
        }
      }).on('fileuploadprogress', (e, data) => {
        il.waiter.setBytes(data.loaded, data.total);
      }).on('fileuploadfail', (e, data) => {
        clearInterval(this.session_refresher);
        $.ajax({
          type: 'post',
          url: url_upload_failed,
          data: { filename: file.name, message: e.errorThrown }
        });
        il.waiter.hide();
        this.file_in_progress = undefined;
        alert('Error: ' + data.errorThrown)
      });

      // session refresher every 5 minutes for very long uploads 300000
      this.session_refresher = setInterval(() => {
        $.get(url_session_refresh);
      }, 300000);

      // init chunked upload
      $input.fileupload('send', {
        files: file,
        url: response.uploadUrl,
        type: 'PUT',
        maxChunkSize: Math.min(chunk_size, file.size - 1),
        multipart: false
      });
    }).fail((err) => {
      clearInterval(this.session_refresher);
      il.waiter.hide();
      const error_json = JSON.parse(err.responseText);
      alert(error_json.error.message + "<br>Please contact an administrator.");
    });
  });

  // abort listener
  $(window).bind('beforeunload', () => {
    if (typeof this.file_in_progress !== 'undefined') {
      return 'Upload in progress. Are you sure you want to leave?';
    }
    return undefined;
  });
  $(window).bind('unload', () => {
    if (typeof this.file_in_progress !== 'undefined') {
      $.ajax({
        type: 'post',
        url: url_upload_aborted,
        data: { filename: this.file_in_progress.name }
      });
    }
  })
}


function executeFunctionByName(functionName, context /*, args */) {
  var args = Array.prototype.slice.call(arguments, 2);
  var namespaces = functionName.split(".");
  var func = namespaces.pop();
  for (var i = 0; i < namespaces.length; i++) {
    context = context[namespaces[i]];
  }
  return context[func].apply(context, args);
}
