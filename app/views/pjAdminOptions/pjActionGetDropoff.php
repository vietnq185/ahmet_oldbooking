<select class="pj-form-field w400 pj-install-config" id="install_dropoff_id" name="install_dropoff_id">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	foreach($tpl['dropoff_arr'] as $k => $v)
	{
		?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php
	} 
	?>
</select>
