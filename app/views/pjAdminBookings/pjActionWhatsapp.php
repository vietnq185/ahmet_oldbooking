<form method="post" class="form pj-form" id="frmSendWs">
	<input type="hidden" name="id" value="<?php echo $tpl['ws_arr']['id'];?>" />
	<input type="hidden" name="booking_id" value="<?php echo $tpl['arr']['id'];?>" />
	<p>
		<label class="title"><?php __('lblSelectLanguage', false, true); ?></label>
		<span class="inline-block">
			<select name="locale_id" class="pj-form-field w250">
				<?php foreach ($tpl['locale_arr'] as $locale) { ?>
					<option value="<?php echo $locale['id'];?>" <?php echo $tpl['locale_id'] == $locale['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($locale['name']);?></option>
				<?php } ?>
			</select>
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblSendWhatsappTo', false, true); ?></label>
		<span class="inline-block">
			<input type="text" name="customer_phone" id="customer_phone" class="pj-form-field w450" value="<?php echo pjSanitize::html($tpl['arr']['c_dialing_code'].$tpl['arr']['c_phone']); ?>" />
		</span>
	</p>
	<p>
        <label class="title"><?php __('lblWMMessage'); ?></label>
		<span class="inline_block">
			<textarea name="message" class="pj-form-field w450 h200"><?php echo htmlspecialchars(stripslashes(@$tpl['lang_message'])); ?></textarea>
		</span>
    </p>
</form>