jQuery(document).ready(function($){
  $('.uploadTrigger').live('click', function(){
    var t = $(this),
        p = t.parent(),
        target = $('.ntzUploadTarget', p);

      var oldSendToEditor = window.send_to_editor;

        var ntzUploadTarget   = t.parent().find('.ntzUploadTarget'),
            ntzUploadTargetId = target,
            attach_id_pattern = new RegExp("send\[[0-9]*\]"),
            ntzGetId;
        window.clearInterval( ntzGetId );

        ntzGetId = window.setInterval(function(){

          if( !$('#TB_iframeContent').length ){ window.clearInterval( ntzGetId ); return; };

          var iframe   = $( $('#TB_iframeContent')[0].contentWindow.document.body ),
              uploadID = $('.savesend .button', iframe).filter(function(){ return $(this).closest('table:visible').length; }).attr('id');

          if( uploadID ){
            uploadID = uploadID.replace('send[', '');
            uploadID = uploadID.replace(']', '');

            if( ntzUploadTargetId && ntzUploadTargetId.val() !== uploadID ){
              ntzUploadTargetId.val( uploadID ).trigger( 'has_id' );
            }

          }
        }, 500);

      window.send_to_editor = function( html ) {

        imgurl = $('img',html).attr('src') || $(html).attr('src');
        //ntzUploadTarget.val(imgurl).focus().blur();
        
        //ntzUploadTarget = '';
        tb_remove();
        window.clearInterval( ntzGetId );

        ntzUploadTargetId.trigger( 'upload_complete' );

        if(typeof(oldSendToEditor)=='function') { 
          window.send_to_editor = oldSendToEditor;
        }
        console.log(ajaxurl);
        $.getJSON( ajaxurl, {
          action : 'get_image_versions',
          img_id : ntzUploadTargetId.val()
        }, function(json){
          console.log(json);
          $( '.upload_preview', p ).empty().append( json.thumbnail );
        } );

      };
      tb_show('Upload file', 'media-upload.php?type=image&amp;TB_iframe=true&amp;width=640&amp;height=600');

    return false;
  });

});