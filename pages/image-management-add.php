<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$ziss_errors = array();
$ziss_success = '';
$ziss_error_found = false;

$form = array(
	'ziss_title' => '',
	'ziss_img_sm' => '',
	'ziss_img_bg' => '',
	'ziss_width' => '',
	'ziss_height' => '',
	'ziss_fade' => '',
	'ziss_scale' => '',
	'ziss_position' => '',
	'ziss_group' => '',
	'ziss_status' => ''
);

if (isset($_POST['ziss_form_submit']) && sanitize_text_field($_POST['ziss_form_submit']) == 'yes') {
	check_admin_referer('ziss_form_add');
	
	$form['ziss_img_sm'] = isset($_POST['ziss_img_sm']) ? esc_url_raw($_POST['ziss_img_sm']) : '';
	if ($form['ziss_img_sm'] == '') {
		$ziss_errors[] = __('Please enter the image path.', 'zoom-image-simple-script');
		$ziss_error_found = true;
	}
	
	$form['ziss_img_bg'] = isset($_POST['ziss_img_bg']) ? esc_url_raw($_POST['ziss_img_bg']) : '';
	if ($form['ziss_img_bg'] == '') {
		$ziss_errors[] = __('Please enter the image path.', 'zoom-image-simple-script');
		$ziss_error_found = true;
	}
	
	$form['ziss_title'] = isset($_POST['ziss_title']) ? sanitize_text_field($_POST['ziss_title']) : '';
	$form['ziss_width'] = isset($_POST['ziss_width']) ? intval($_POST['ziss_width']) : '';
	$form['ziss_height'] = isset($_POST['ziss_height']) ? intval($_POST['ziss_height']) : '';
	$form['ziss_fade'] = isset($_POST['ziss_fade']) ? intval($_POST['ziss_fade']) : '';
	$form['ziss_scale'] = isset($_POST['ziss_scale']) ? intval($_POST['ziss_scale']) : '';
	$form['ziss_position'] = isset($_POST['ziss_position']) ? sanitize_text_field($_POST['ziss_position']) : '';
	
	$form['ziss_group'] = isset($_POST['ziss_group']) ? sanitize_text_field($_POST['ziss_group']) : '';
	if ($form['ziss_group'] == '') {
		$form['ziss_group'] = isset($_POST['ziss_group_txt']) ? sanitize_text_field($_POST['ziss_group_txt']) : '';
	}
	if ($form['ziss_group'] == '') {
		$ziss_errors[] = __('Please enter the image group.', 'zoom-image-simple-script');
		$ziss_error_found = true;
	}

	$form['ziss_status'] = isset($_POST['ziss_status']) ? sanitize_text_field($_POST['ziss_status']) : '';
	
	if ($ziss_error_found == false)
	{
		$status = ziss_cls_dbquery::ziss_action_ins($form, "insert");
		if($status == 'inserted') {
			$ziss_success = __('New image details was successfully added.', 'zoom-image-simple-script');
		}
		else {
			$ziss_errors[] = __('Oops, something went wrong. try again.', 'zoom-image-simple-script');
			$ziss_error_found = true;
		}
		
		$form = array(
			'ziss_title' => '',
			'ziss_img_sm' => '',
			'ziss_img_bg' => '',
			'ziss_width' => '',
			'ziss_height' => '',
			'ziss_fade' => '',
			'ziss_scale' => '',
			'ziss_position' => '',
			'ziss_group' => '',
			'ziss_status' => ''
		);
	}
}

if ($ziss_error_found == true && isset($ziss_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $ziss_errors[0]; ?></strong></p></div><?php
}
if ($ziss_error_found == FALSE && strlen($ziss_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $ziss_success; ?>
	<a href="<?php echo ZISSD_ADMIN_URL; ?>"><?php _e('Click here', 'zoom-image-simple-script'); ?></a> <?php _e('to view the details', 'zoom-image-simple-script'); ?>
	</strong></p></div><?php
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            $('#ziss_img_sm').val(img_imageurl);
			$('#ziss_title').val(img_imagetitle);
        });
    });
	$('#upload-btn1').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
			$('#ziss_img_bg').val(img_imageurl);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery');
wp_enqueue_media();
?>
<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Add zoom details', 'zoom-image-simple-script'); ?></h1><br /><br />
	<form name="ziss_form" method="post" action="#" onsubmit="return _ziss_submit()" >      
	    
	  <label for="tag-image"><strong><?php _e('Small image', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_img_sm" type="text" id="ziss_img_sm" value="" size="60" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
      <p><?php _e('Small image, thumbnail image for the display.', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-image"><strong><?php _e('Big image', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_img_bg" type="text" id="ziss_img_bg" value="" size="60" />
	  <input type="button" name="upload-btn1" id="upload-btn1" class="button-secondary" value="Upload">
      <p><?php _e('Big image, original higher resolution image to display on zoom.', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-link"><strong><?php _e('Image title', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_title" type="text" id="ziss_title" value="" size="60" />
      <p><?php _e('Enter title for your zoom image.', 'zoom-image-simple-script'); ?></p>
	  
	  <label for="tag-image"><strong><?php _e('Image width', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_width" type="text" id="ziss_width" value="0" />
      <p><?php _e('The width of the zoom area interface (0 = auto).', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-image"><strong><?php _e('Image height', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_height" type="text" id="ziss_height" value="0" />
      <p><?php _e('The height of the zoom area interface (0 = auto).', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-image"><strong><?php _e('Image fade', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_fade" type="text" id="ziss_fade" value="500" />
      <p><?php _e('The duration of the fade in effect, in milliseconds (1000 = 1 second).', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-image"><strong><?php _e('Image scale', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_scale" type="text" id="ziss_scale" value="0" />
      <p><?php _e('Sets the dimensions of the enlarged image when viewers zoom in (0 = auto).', 'zoom-image-simple-script'); ?> </p>
	  
	  <label for="tag-image"><strong><?php _e('Image position', 'zoom-image-simple-script'); ?></strong></label>
      <input name="ziss_position" type="text" id="ziss_position" value="" disabled />
      <p><?php _e('Set this option to true if your thumbnail image is wrapped inside a fixed parent element (true/false).', 'zoom-image-simple-script'); ?> </p>
	  
      <label for="tag-select-gallery-group"><strong><?php _e('Image group', 'zoom-image-simple-script'); ?></strong></label>
		<select name="ziss_group" id="ziss_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$groups = array();
			$groups = ziss_cls_dbquery::ziss_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo stripslashes($group["ziss_group"]); ?>">
						<?php echo stripslashes($group["ziss_group"]); ?>
					</option>
					<?php
				}
			}
			?>
		</select>
		(or) 
	   	<input name="ziss_group_txt" type="text" id="ziss_group_txt" value="" maxlength="10" onkeyup="return _ziss_numericandtext(document.ziss_form.ziss_group_txt)" />
      <p><?php _e('This is to group the images. Select your group.', 'zoom-image-simple-script'); ?></p>
	  
      <label for="tag-display-status"><strong><?php _e('Display', 'zoom-image-simple-script'); ?></strong></label>
      <select name="ziss_status" id="ziss_status">
        <option value='Yes'>Yes</option>
        <option value='No'>No</option>
      </select>
      <p><?php _e('Do you want the image to show in the frontend?', 'zoom-image-simple-script'); ?></p>
	  
      <input name="ziss_id" id="ziss_id" type="hidden" value="">
      <input type="hidden" name="ziss_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'zoom-image-simple-script'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_ziss_redirect()" value="<?php _e('Cancel', 'zoom-image-simple-script'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_ziss_help()" value="<?php _e('Help', 'zoom-image-simple-script'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('ziss_form_add'); ?>
    </form>
</div>
</div>