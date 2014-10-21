jQuery(function($){
	var insert_button = $('.insert-pictures');
	
	insert_button.live('click', function(e) {
		var scroller_image_ids = $(this).parents('.widget').find('.tk-scroller-image-ids');
		var tkscorllslider_pictures_box = $(this).parents('.widget').find('.tkscorllslider-pictures ul');
		var tk_wp_media_box;
		tkscorllslider_pictures_box.sortable();
		tkscorllslider_pictures_box.on('sortupdate', function(){
			scroller_image_ids.val(reorder_images($(this)));
		});
		e.preventDefault();
		
		tk_wp_media_box = wp.media.frames.file_frame = wp.media({
			title: "Select images for Scroller",
			button: {
				text: 'Insert'
			},
			multiple: true
		});
		tk_wp_media_box.on('select', function(){
			attachment = tk_wp_media_box.state().get('selection');
			var count = 0;
			var image_ids = "";
			while(attachment.length > count) {
				obj = attachment.toJSON()[count];
				image_ids = image_ids + obj.id;
				count = count + 1;
				if(attachment.length > count) {
					image_ids = image_ids + ',';
				}
			}
			send_image_id(image_ids, tkscorllslider_pictures_box);
			scroller_image_ids.val(image_ids);
		});
		tk_wp_media_box.open();
	});

	// For existing list
	$('.tkscorllslider-pictures ul').sortable();
	$('.tkscorllslider-pictures ul').on('sortupdate', function(){
		var scroller_image_ids = $(this).parents('.widget').find('.tk-scroller-image-ids');
		scroller_image_ids.val(reorder_images($(this)));
	});
});

function send_image_id(image_ids, destination) {
	jQuery.ajax({
		url: tk_scroller_ajax.admin_ajax,
		type: 'POST',
		data: {
			action: 'tk_scroller',
			image_ids: image_ids
		},
		success: function(data){
			destination.html(data);
		}
	});
}

function reorder_images(tkscorllslider_pictures_box) {
	var image_ids = '';
	var length = tkscorllslider_pictures_box.find('li');
	var index = 1;
	console.log(length.length);
	length = length.length;
	tkscorllslider_pictures_box.find('li').each(function(){
		image_ids = image_ids + jQuery(this).attr('data-image-id');
		if(index < length) {
			image_ids = image_ids + ",";
		}
		index = index + 1;
	});
	return image_ids;
}