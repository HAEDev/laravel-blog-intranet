$(document).ready(function() {
    const editor = Jodit.make('#post_content', {
        uploader: {
            url: jodieEditorConnectorURL,
            data: {
                action: 'fileUpload'
            }
        },
        filebrowser: {
            ajax: {
                url: jodieEditorConnectorURL
            }
        }
    });
});