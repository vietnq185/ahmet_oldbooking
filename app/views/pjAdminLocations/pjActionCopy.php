<?php
if(count($tpl['dropoff_arr']) > 0)
{
	$yesno = __('_yesno', true);
	foreach($tpl['dropoff_arr'] as $k => $dropoff)
	{
		$index = 'tr_' . rand(1, 999999); 
		?>
		<tr class="tr-location-row" data-index="<?php echo $index;?>">
			<td>
				<div class="p">
					<select name="icon[<?php echo $index; ?>]" class="pj-form-field required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<option value="airport" <?php echo ($dropoff['icon'] == 'airport') ? 'selected="selected"' : ''; ?>><?php __('lblIconAirport'); ?></option>
						<option value="train" <?php echo ($dropoff['icon'] == 'train') ? 'selected="selected"' : ''; ?>><?php __('lblIconTrain'); ?></option>
						<option value="city" <?php echo ($dropoff['icon'] == 'city') ? 'selected="selected"' : ''; ?>><?php __('lblIconCity'); ?></option>
						<option value="skiing" <?php echo ($dropoff['icon'] == 'skiing') ? 'selected="selected"' : ''; ?>><?php __('lblIconSkiing'); ?></option>
					</select>
				</div>
			</td>
			<td>
				<?php if (isset($_GET['type']) && $_GET['type'] == 'location_price') { ?>
					<input type="hidden" name="dropoff_ids[<?php echo $index;?>]" value="<?php echo $dropoff['id'];?>" />
				<?php } ?>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $index; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $index; ?>]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$dropoff['i18n'][$v['id']]['location'])); ?>"/>
						</span>
					</p>
					<?php
				} 
				?>
			</td>
			<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $index; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][address][<?php echo $index; ?>]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$dropoff['i18n'][$v['id']]['address'])); ?>"/>
						</span>
					</p>
					<?php
				} 
				?>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="duration_<?php echo $index;?>" name="duration[<?php echo $index;?>]" class="pj-form-field field-int w60 pj-positive-number" value="<?php echo $dropoff['duration']; ?>"/>
					</span>
				</p>
				
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="distance_<?php echo $index;?>" name="distance[<?php echo $index;?>]" class="pj-form-field field-int w60 pj-positive-number" value="<?php echo $dropoff['distance']; ?>"/>
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<select name="airport[<?php echo $index; ?>]" id="airport_<?php echo $index; ?>" class="pj-form-field w80">
	                        <option value="0"<?php echo 0 == $dropoff['is_airport'] ? ' selected="selected"' : NULL; ?>><?php echo $yesno['F']; ?></option>
	                        <option value="1"<?php echo 1 == $dropoff['is_airport'] ? ' selected="selected"' : NULL; ?>><?php echo $yesno['T']; ?></option>
	                    </select>
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<a href="#" class="pj-remove-dropoff"></a>
					</span>
				</p>
			</td>
		</tr>
		<?php
	}
} 
?>