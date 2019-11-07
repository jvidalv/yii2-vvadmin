
$(document).on('click', 'a', function(){

   console.log( $('#article-content_ifr').contents().find("html").html());
   let tag = $($(this).attr('href'));
   console.log($("#tinymce"))
    //$('html,body').animate({scrollTop: tag.offset().top},'slow');
})
