/**
 * Generate ghost dom on anchor click
 */
$(document).click('.anchor-section', function () {
    makeAnchorsInsideTinyMceWorkable();
});

/**
 * We create a ghost copy of article contents so the anchors outside work
 */
function makeAnchorsInsideTinyMceWorkable() {
    if (!$('#content-ghost-copied').length) {
        let contents = $('#contents-ghost').html();
        $('.tox-edit-area').prepend('<div id="content-ghost-copied">' + contents + '</div>')
    }
}

window.addEventListener("hashchange", function () {
    window.scrollTo(window.scrollX, window.scrollY - 200);
});

/**
 * Upload image function
 */
function uploadImageTiny(cb, value, meta) {

    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    /*
      Note: In modern browsers input[type=\"file\"] is functional without
      even adding it to the DOM, but that might not be the case in some older
      or quirky browsers like IE, so you might want to add it to the DOM
      just in case, and visually hide it. And do not forget do remove it
      once you do not need it anymore.
    */

    input.onchange = function () {
        const file = this.files[0];

        const reader = new FileReader();
        reader.onload = function () {
            /*
              Note: Now we need to register the blob in TinyMCEs image blob
              registry. In the next release this part hopefully won't be
              necessary, as we are looking to handle it internally.
            */
            const id = 'blobid' + (new Date()).getTime();
            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
            const base64 = reader.result.split(',')[1];
            let blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            /* call the callback and populate the Title field with the file name */
            cb(blobInfo.blobUri(), {title: file.name});
        };
        reader.readAsDataURL(file);
    };

    input.click();
}

/**
 * Capture form post and modify its default logic
 */
$(document).submit(function () {
    // New input with number of words serverd by tinymce
    $('form').append(`<input name="Article[word_count]" value="${tinymce.activeEditor.plugins.wordcount.body.getWordCount()}" />`);

    return true;
});
