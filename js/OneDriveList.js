function OneDriveList(url_rename) {
    var url_rename = url_rename;

    var clicked_rename = false;

    //Ajax request to rename a file/folder
    this.rename = function (item_id, title) {

        if(!this.clicked_rename)
        {
            this.clicked_rename = true;
            il.CloudFileList.hideMessage();

            $.ajax({
                type: "POST",
                url: url_rename.replace(/&amp;/g, '&'),
                data: { 'id': item_id, 'title': title}
            }).done(function (return_data) {
                if (return_data.success) {
                    self.clicked_rename = true;
                    il.CloudFileList.showDebugMessage("rename: Form successfully created per ajax. id=" + il.CloudFileList.getCurrentId());
                    il.CloudFileList.hideBlock(il.CloudFileList.getCurrentId());
                    il.CloudFileList.hideItemCreationList();
                    $("#xcld_blocks").append(return_data.content);
                    $("input[name='cmd[rename]']").click(function () {
                        il.CloudFileList.showProgressAnimation();
                    });
                }
                else {
                    if (return_data.message) {
                        il.CloudFileList.showDebugMessage("rename: Form not successfully created per ajax. message=" + return_data.message);
                        il.CloudFileList.showMessage(return_data.message);
                    }
                    else {
                        il.CloudFileList.showDebugMessage("rename: Form not successfully created per ajax. data=" + return_data);
                        il.CloudFileList.showMessage(return_data);
                    }
                }
            });
        }
    }

    this.afterRenamed = function (data) {
        this.clicked_rename = false;
        if (data.success || data.status == "cancel") {
            var callback = function (self, data) {
                console.log(data.id);
                $("#cld_rename").remove();
                il.CloudFileList.showItemCreationList();

                if (data.status == "cancel") {
                    il.CloudFileList.showDebugMessage("afterRenamed: Renaming cancelled.");
                }
                else if (data.success) {
                    $('#xcld_folder_' + data.id + ' a.il_ContainerItemTitle').text(data.title);
                    $('#xcld_file_' + data.id + ' a.il_ContainerItemTitle').text(data.title);
                    il.CloudFileList.showDebugMessage("afterRenamed: Item successfully renamed.");
                    il.CloudFileList.showMessage(data.message);
                }
            }
            if(data.success)
            {
                il.CloudFileList.showCurrent(true, callback, data);
            }
            else
            {
                il.CloudFileList.showCurrent(false, callback, data);
            }
        }
        else {
            if (data.message) {
                il.CloudFileList.showDebugMessage("afterRenamed: Renaming of item failed. message=" + data.message);
                il.CloudFileList.showMessage(data.message);
            }
            else {
                il.CloudFileList.showDebugMessage("afterRenamed: Renaming of Item failed. data=" + data);
                il.CloudFileList.showMessage(data);

            }
            display_message = false;
            il.CloudFileList.hideProgressAnimation();
        }

    }
}