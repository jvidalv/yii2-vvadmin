

$(document).click('.anchor-section', function(){
   makeAnchorsInsideTinyMceWorkable();
});

/**
 * We create a ghost copy of article contents so the anchors outside work
 */
function makeAnchorsInsideTinyMceWorkable()
{
   if(!$('#content-ghost-copied').length) {
      let contents = $('#contents-ghost').html();
      $('.tox-edit-area').prepend('<div id="content-ghost-copied">' + contents + '</div>')
   }
}

window.addEventListener("hashchange", function () {
   window.scrollTo(window.scrollX, window.scrollY - 200);
});



