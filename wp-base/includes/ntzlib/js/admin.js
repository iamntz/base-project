jQuery(document).ready(function($){

  $('.ntz_colorpicker').each(function(){
    var color = $(this);
    color.css({
      backgroundColor:'#'+color.val(),
      color          :'#'+color.val()
    });

    color.ColorPicker({
      color:color.val(),
      livePreview:true,
      onChange: function(hsb, hex, rgb, el) {
        color.val(hex).css({
          backgroundColor:'#'+hex,
          color          :'#'+hex
          //color: ( ( rgb.r + rgb.g + rgb.b) <= 381 ? '#fff' : '#000' )
        });
        
      },
      onBeforeShow: function () {
        $(this).ColorPickerSetColor(this.value);
      }
    }).bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
    });
    
  });


  $( '.upload_preview' ).live('dblclick', function(){
    var t = $(this);
    if( confirm( "Remove Image?" ) ){
      t.empty();
      t.parent().find('.ntzUploadTarget').val('');
    }
  });

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
        $.getJSON( ajaxurl, {
          action : 'get_image_versions',
          img_id : ntzUploadTargetId.val()
        }, function(json){
          var img_size = $( '.upload_preview', p ).data('imgsize');
          $( '.upload_preview', p ).empty().append( json[img_size] );
        } );

      };
      tb_show('Upload file', 'media-upload.php?type=image&amp;TB_iframe=true&amp;width=640&amp;height=600');

    return false;
  });


  $('.ntzRestoreDefault').click(function(){
    var t     = $(this),
        field = t.prev(),
        type  = field[0].tagName.toLowerCase();
    if( type == 'select' ){
      $('option', field).filter( function(){ return this.value == t.data('default'); }).attr('selected', true);
    }else{
      field.val( t.data('default') );
    }
    t.hide();
    return false;
  })
});