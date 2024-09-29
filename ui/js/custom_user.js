/*$(document).ready(function(){

    $('#create-email').keyup(function(){
        var email = $('#create-email').val();
        var username = $('#create-username').val();
        var ajx_url = $('#create-email').attr('data-url');
        $.ajax({
            url: ajx_url,
            type: 'GET',
            cache: false,
            data:{email:email,username:username},
            success: function (result) {
                alert(result);
            }
        });
    });
   

});

*/