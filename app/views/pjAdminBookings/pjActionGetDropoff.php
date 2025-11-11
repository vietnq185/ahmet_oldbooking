<select name="dropoff_id" id="dropoff_id" class="pj-form-field w400 required">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	foreach($tpl['dropoff_arr'] as $v)
	{
		?><option value="<?php echo $v['id'];?>" data-is-airport="<?php echo (int) $v['is_airport']; ?>" data-duration="<?php echo !empty($v['duration']) ? $v['duration'] . ' ' . strtolower(__('lblMinutes', true, false)) : null; ?>" data-distance="<?php echo !empty($v['distance']) ? $v['distance'] . ' ' . strtolower(__('lblKm', true, false)) : null; ?>"><?php echo $v['location'];?></option><?php
	} 
	?>
</select>
