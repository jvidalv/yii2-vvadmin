let recarregar = 0;

$(document).ready(function () {

    /* modal de pujada de fitxers */
    $("#modal").on("hidden.bs.modal", function () {
        /* llimpiem lo modal al tancarlo */
        $('#media-input').fileinput('clear');
        /* recarreguem lo llistat */
        $.pjax.reload({container: '#recarregar-ajax'})
    });
    /* modal de modificacio de media */
    $("#modal-media").on("hidden.bs.modal", function () {
        if (recarregar) $.pjax.reload({container: '#recarregar-ajax'});
        recarregar = 0;
    });

})

/* carreguem lo modal de modificacio de la imatge */
$('[data-modificar]').click(function (event) {
    event.preventDefault();
    const idMedia = $(this).data('media-id');
    $('#modal-media').find('.modal-body').load('/media/update?id=' + idMedia);
    $('#modal-media').modal('toggle');
});

/* carreguem lo modal de modificacio de la imatge */
// $('[data-descarregar]').click(function(event){
//   event.preventDefault();
//   var idMedia = $(this).data('media-id')
//   $.ajax({
//       url: '/media/descarregar?id='+idMedia,
//       type: 'get',
//       dataType: 'json',
//   })
// })

/* modal ajax de modificacio de fitxers */
$(document).on("submit", "#media-form", function () {

    event.preventDefault(); // parem el post
    const data = $(this).serializeArray();
    const url = $(this).attr('action');

    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data
    })
        .done(function (response) {
            recarregar = 1;
            if (response === true) {
                $('#alert-media-form').show('slow');
            }
        })
        .fail(function () {
            console.log("error");
        });
});
