jQuery(document).ready(function($){

  $('.ntz_colorpicker, .ntzColorpicker').each(function(){
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

/* Usage: 
  <p> <!-- can be any element -->
    <!-- 
      add this in functions.php: 
      add_image_size( 'img-upload-preview', WIDTH, HEIGHT, true );  
    -->
    <span class="upload_preview" data-imgsize="img-upload-preview"></span> 
    <input type="hidden" name="img_id" value="" class="ntzUploadTarget" />
    <button class="button-secondary uploadTrigger"><span class="add">Add</span><span class="remove">Remove</span> Image</button>
  </p>
*/

  $( '.upload_preview' ).live('dblclick removeImage', function(){
    var t = $(this);
    if( confirm( "Remove Image?" ) ){
      t.empty();
      t.parent().find('.ntzUploadTarget').val('');
      t.parent().removeClass('hasImage');
    }
  });

  $('.ntzUploadTarget').on('show_preview', function(){
    var t = $(this),
        p = t.parent(),
        img_id = t.val();
    if( !img_id || img_id == 0 ){ return; }

    $.getJSON( ajaxurl, {
      action : 'get_image_versions',
      img_id : t.val()
    }, function(json){
      var img_size = $( '.upload_preview', p ).data('imgsize') || 'thumbnail';
      $( '.upload_preview', p ).empty().append( json[img_size] );
      p.addClass('hasImage');
    } );
  });

  $('.uploadTrigger, .ntzUploadTrigger').live('click', function(){
    var t = $(this),
        p = t.parent(),
        target = $('.ntzUploadTarget', p);

    if( p.hasClass('hasImage') ){
      $('.upload_preview', p).trigger('removeImage');
      return false;
    }

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

          if( uploadID ){ //  TODO: make custom image URL works!
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
        ntzUploadTargetId.trigger('show_preview');
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