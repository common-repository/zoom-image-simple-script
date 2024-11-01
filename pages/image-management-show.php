<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_ziss_display']) && $_POST['frm_ziss_display'] == 'yes') {
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$ziss_success = '';
	$ziss_success_msg = false;
	$result = ziss_cls_dbquery::ziss_count($did);
	
	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'zoom-image-simple-script'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && sanitize_text_field($_GET['ac']) == 'del' && isset($_GET['did']) && intval($_GET['did']) != '') {
			check_admin_referer('ziss_form_show');
			ziss_cls_dbquery::ziss_delete($did);
			$ziss_success_msg = true;
			$ziss_success = __('Selected record was successfully deleted.', 'zoom-image-simple-script');
		}
	}
	
	if ($ziss_success_msg == true) {
		?><div class="updated fade"><p><strong><?php echo $ziss_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Zoom image', 'zoom-image-simple-script'); ?>
	<a class="add-new-h2" href="<?php echo ZISSD_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'zoom-image-simple-script'); ?></a></h2><br />
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = ziss_cls_dbquery::ziss_select_bygroup("");
	?>
	<form name="frm_ziss_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Image', 'zoom-image-simple-script'); ?></th>
			<th scope="col"><?php _e('Title', 'zoom-image-simple-script'); ?></th>
            <th scope="col"><?php _e('Group', 'zoom-image-simple-script'); ?></th>
            <th scope="col"><?php _e('Status', 'zoom-image-simple-script'); ?></th>
			<th scope="col"><?php _e('Short Code', 'zoom-image-simple-script'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Image', 'zoom-image-simple-script'); ?></th>
			<th scope="col"><?php _e('Title', 'zoom-image-simple-script'); ?></th>
            <th scope="col"><?php _e('Group', 'zoom-image-simple-script'); ?></th>
            <th scope="col"><?php _e('Status', 'zoom-image-simple-script'); ?></th>
			<th scope="col"><?php _e('Short Code', 'zoom-image-simple-script'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td>
						<a href="<?php echo $data['ziss_img_bg']; ?>" target="_blank">
							<img src="<?php echo $data['ziss_img_sm']; ?>" width="60"  />
						</a>
						<?php if($data['ziss_img_bg'] <> '') { ?>
						<a href="<?php echo $data['ziss_img_bg']; ?>" target="_blank"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/link-icon.gif"  /></a>
						<?php } ?>
					</td>
					<td>
						<?php echo $data['ziss_title']; ?>
						<div class="row-actions">
							<span class="edit"><a title="Edit" href="<?php echo ZISSD_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['ziss_id']; ?>"><?php _e('Edit', 'zoom-image-simple-script'); ?></a> | </span>
							<span class="trash"><a onClick="javascript:_ziss_delete('<?php echo $data['ziss_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'zoom-image-simple-script'); ?></a></span> 
						</div>
					</td>
					<td><?php echo $data['ziss_group']; ?></td>
					<td><?php echo ziss_cls_dbquery::ziss_common_text($data['ziss_status']); ?></td>
					<td>[ziss-zoom-image id="<?php echo $data['ziss_id']; ?>"]</td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="5" align="center"><?php _e('No records available', 'zoom-image-simple-script'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('ziss_form_show'); ?>
		<input type="hidden" name="frm_ziss_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo ZISSD_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'zoom-image-simple-script'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2021/03/21/zoom-image-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'zoom-image-simple-script'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2021/03/21/zoom-image-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'zoom-image-simple-script'); ?>" /></a>
	  </div>
	</div>
</div>