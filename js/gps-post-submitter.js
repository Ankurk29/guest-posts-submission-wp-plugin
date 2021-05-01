jQuery( document ).ready( function ( $ ) {
    
    $('#post-submission-form').find('.post-ajax-loader').hide();
    
    $( '#post-submission-form' ).on( 'submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData();
        
        var data = $(this).serializeArray();
        $.each(data,function(key,input){
            formData.append(input.name,input.value);
        });
        
        var file = $(document).find('input[type="file"]');
        $.each($(file), function(i, obj) {
            $.each(obj.files,function(j,file){
                formData.append('files[' + j + ']', file);
            })
        });
        
        formData.append('action', 'gps_multidots_save_post_into_db');

        $.ajax({
            method: "POST",
            url: POST_SUBMITTER.root,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function ( xhr ) {
                $('#post-submission-form').find('.post-ajax-loader').show();
                xhr.setRequestHeader( 'X-WP-Nonce', POST_SUBMITTER.nonce );
            },
            success : function( response ) {
                // console.log(response);
                $('#post-submission-form').find('.post-ajax-loader').hide();
                if( response.data.status === 'OK' ) {
                    alert( response.data.message );
                    $("#post-submission-form")[0].reset();
                } else {
                    alert( response.data );
                }
            },
            fail : function( response ) {
                $(this).find('.post-ajax-loader').hide();
                console.log( response );
            }
        });

    });

} );
