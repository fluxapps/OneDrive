initChunkedUpload = (input_id, form_id, url_fetch_upload_url, chunk_size, after_upload_callback) => {
  const file_picker = document.getElementById(input_id);
  let upload_in_progress = false;
  $('#form_' + form_id).on('submit', (event) => {
    console.log('start chunked upload');
    event.preventDefault();
    il.waiter.show();
    upload_in_progress = true;

    let reader = new FileReader();
    let file = file_picker.files[0];
    $.ajax({
      type: 'post',
      url: url_fetch_upload_url,
      data: { name: file.name }
    }).success((response) => {
      response = JSON.parse(response);
      $('#' + input_id)
      .on('fileuploaddone', (e, data) => {
        il.waiter.hide();
        upload_in_progress = false;
        if (typeof after_upload_callback !== 'undefined') {
          executeFunctionByName(after_upload_callback, window, file);
        }
      }).on('fileuploadprogress', (e, data) => {
        il.waiter.setPercentage(data.loaded / data.total * 100);
      }).on('fileuploadfail', (e, data) => {
        il.waiter.hide();
        upload_in_progress = false;
        alert('Error: ' + data.errorThrown)
      });

      $('#' + input_id).fileupload();
      $('#' + input_id).fileupload('send', {
        files: file,
        url: response.uploadUrl,
        type: 'PUT',
        maxChunkSize: Math.min(chunk_size, file.size - 1),
        multipart: false
      });
    }).fail((err) => {
      il.waiter.hide();
      alert('Error: ' + err.message);
      console.log(err.responseText);
    });
  })
  $(window).bind('beforeunload', () => {
    if (upload_in_progress) {
      // TODO: send abort
      return 'Upload in progress. Are you sure you want to leave?';
    }
    return undefined;
  });
}

function executeFunctionByName(functionName, context /*, args */) {
  var args = Array.prototype.slice.call(arguments, 2);
  var namespaces = functionName.split(".");
  var func = namespaces.pop();
  for(var i = 0; i < namespaces.length; i++) {
    context = context[namespaces[i]];
  }
  return context[func].apply(context, args);
}
