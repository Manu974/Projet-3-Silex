  function count_carac(){
        $('#comment_content').on('keyup',function(){
            var maxCaracter = "200";
            var nombreCaracter = $(this).val().length;

            var nombreMots = jQuery.trim($(this).val()).split(' ').length;
            console.log(nombreMots);

            if($(this).val() === '') {
                nombreMots = 0 ;
            }

            var msg = ' '+ nombreMots + ' mot(s) | ' + nombreCaracter + ' Caractere(s) / 200';
            $('#compteur2').text(msg);

            if(nombreCaracter > 200) { 
                $('#compteur2').removeClass('label-success').addClass('label-danger');
                $('#comment_content').attr('maxlength',maxCaracter);
            }

            else{
                $('#compteur2').removeClass('label-danger').addClass('label-success');
            }
        });
    } 

    $(document).ready(function() {
        count_carac();
    });