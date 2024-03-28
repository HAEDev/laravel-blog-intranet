$(function() {

    var isAdvancedUpload = function() {
        var div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();

    if(isAdvancedUpload) {
        $('.drag-upload').addClass("has-drag");
        var $form = $('.drag-upload').parents("form");

        $form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        })
            .on('dragover dragenter', function() {
                $form.addClass('is-dragover');
            })
            .on('dragleave dragend drop', function() {
                $form.removeClass('is-dragover');
            })
            .on('drop', function(e) {
                document.querySelector('#images-upload').files = e.originalEvent.dataTransfer.files;
            });
    }

    var pendingImagesContainer = $(".pending-images");

    $("#images-upload").on("change", function() {
        previewImageFiles();
    });

    // Allows image files to be previewed
    function previewImageFiles() {
        pendingImagesContainer.empty();
        var files   = document.querySelector('#images-upload').files;
        var count   = 0;

        function readAndPreview(file) {
            console.log(file);
            if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var template = "<div class='col-sm-6'>\n" +
                        "               <div class='pending'>\n" +
                        "                    <div class='pending-image text-center'>\n" +
                        "                        <img src='"+this.result+"' alt='' />\n" +
                        "                    </div>\n" +
                        "                    <div class='form'>\n" +
                        "                        <div class='form-group'>\n" +
                        "                            <label class='control-label'>Image Caption</label>\n" +
                        "                            <input type='text' class='form-control' name='caption["+file.name+"]' />\n" +
                        "                        </div>\n" +
                        "                        <div class='form-group'>\n" +
                        "                            <label class='control-label'>Alt Text</label>\n" +
                        "                            <input type='text' class='form-control' name='alt_text["+file.name+"]' />\n" +
                        "                        </div>\n" +
                        "                    </div>\n" +
                        "                    <div class='text-right'>" +
                        "                       <button class='btn btn-sm btn-danger remove-image'>Remove</button>" +
                        "                    </div>\n" +
                        "                </div>\n" +
                        "               </div>";
                    pendingImagesContainer.append( template );
                    count++;
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    }

    var pendingFilesContainer = $(".pending-files");

    $("#files-upload").on("change", function() {
        previewFiles();
    });

    function previewFiles() {
        pendingFilesContainer.empty();
        var files   = document.querySelector('#files-upload').files;
        var count   = 0;

        function readAndPreview(file) {
            console.log(file);
            if ( /\.(xls?x|doc?x|pdf)$/i.test(file.name) ) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var template = "<div class='col-sm-6'>\n" +
                        "               <div class='pending'>\n" +
                        "                    <div class='pending-file text-center'>\n" +
                        "                        "+file.name+"\n" +
                        "                    </div>\n" +
                        "                    <div class='text-right'>" +
                        "                       <button class='btn btn-sm btn-danger remove-image'>Remove</button>" +
                        "                    </div>\n" +
                        "                </div>\n" +
                        "               </div>";
                    pendingFilesContainer.append( template );
                    count++;
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    }

    // Used when previewing files to upload
    $(document).on("click", ".remove-image", function(event) {
        event.preventDefault();
        $(this).parents(".pending").parent().remove();
    });

    // Select script for featured images
    var selectFeatured = $('.select-featured');
    selectFeatured.on("click", function(event) {
        console.log("Click");
        event.preventDefault();
        var id = $(this).data("id");
        var url = $(this).data("url");
        console.log(parent.updateFeaturedImage(id, url));
    });

    // Copy URL button
    var copyUrl = $(".copy-url");
    copyUrl.on('click', function(event) {
        event.preventDefault();

        var copyText = $(this).parent().find("input");
        copyText.select();
        document.execCommand("Copy");
        alert("URL copied to your clipboard!");
    });


    // CKEditor image select feature
    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
        var match = window.location.search.match(reParam) ;

        return (match && match.length > 1) ? match[1] : '' ;
    }

    // Select image dialog box
    $(".ck-select-image").on("click", function(event) {
        event.preventDefault();
        var funcNum = getUrlParam('CKEditorFuncNum');
        var imageUrl = $(this).data("url");
        var imageAlt = $(this).data("alt-text");
        window.opener.CKEDITOR.tools.callFunction(funcNum, imageUrl, function() {
            // Get the reference to a dialog window.
            var element, dialog = this.getDialog();
            // Check if this is the Image dialog window.

            if (dialog.getName() == 'image') {
                // Get the reference to a text field that holds the "alt" attribute.
                element = dialog.getContentElement('info', 'txtAlt');
                // Assign the new value.
                if (element) element.setValue(imageAlt);
            }
        });
        window.close();
    });

    $(".select-file").on("click", function(event){
        var id = $(this).data("id");
        var name = $(this).data("name");
        parent.addFileToArray(id, name);
    });

    setRemoveFileClick()

    window.addFileToArray = function(id, name) {
        if($('#files-table').find('input[value='+id+']').length > 0) {
            alert("File already selected!");
            return;
        }

        $('#files-table > tbody').append('<tr data-id="'+id+'"><td><input type="hidden" name="attached_files['+id+']" value="'+id+'" />'
            +name+'</td><td><input type="text" class="form-control" name="attached_files['+id+'][display_name]"></td>'
            +'<td><a href="#files-table" class="remove-file">Remove</a></td></tr>');

        setRemoveFileClick();

        $('#attached-files').modal("toggle");
    };

    function setRemoveFileClick() {
        $(".remove-file").off("click").on("click", function(event){
            $(this).closest("tr").remove();
        });
    }


    // Update featured image
    window.updateFeaturedImage = function(id, url) {
        $('#featured-image-container').empty().append("<img src='"+url+"' />");
        $('#featured_image').val(id);
        $('#featured-image').modal("toggle");
    };

    $('.existing-tag').on("click", function(event) {
        event.preventDefault();
        $(this).parent().remove();
    });

    $('.existing-categories').on("click", ".existing-category", function(event) {
        event.preventDefault();
        var id = $(this).data("id");
        $(this).parent().remove();
        $('#post_category').find("option[value='"+id+"']").removeClass("hidden");
    });

    $('#post_category').on("change", function(event) {
        event.preventDefault();
        var id = $(this).find(":selected").val();
        var text = $(this).find(":selected").text();

        if(id != 0) {
            $(this).find(":selected").addClass("hidden");

            $('.existing-categories').append('<span class="label label-info tag-label">'+
                '<a class="existing-category" href="#" data-id="'+id+'">' + text + '</a>'+
                '<input type="hidden" name="category[]" value="'+id+'" />'+
                '</span>');

            $(this).val(0);
        }
    });

});