<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form']['passenger'];
$index = pjObject::escapeString($_GET['index']);

$title_arr = pjUtil::getTitles();
$name_titles = __('personal_titles', true, false);
?>
<div class="row">
    <div class="full-width content">
        <header class="f-title color"><?= str_replace('{X}', $STORE['is_return']? 6: 5, __('front_step_x', true, false)) ?><?php __('front_step_passenger_details'); ?></header>
        <div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-info"><div class="vc_message_box-icon"><i class="fa fa-info-circle"></i></div><p><?php __('front_passenger_description'); ?></p></div>
    </div>

    <div class="three-fourth">
        <?php if($tpl['status'] == 'OK'): ?>
            <form id="trPassengerForm_<?php echo $index;?>" action="" method="post">
                <input type="hidden" name="index" value="<?= $index ?>">

                <div class="f-row">
                    <div class="one-half">
                        <div class="one-third">
                            <label for="title"><?php __('front_title'); ?></label>
                            <select name="title" id="title" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <option value="">---</option>
                                <?php foreach($title_arr as $v): ?>
                                    <option value="<?= $v ?>"<?= $FORM['title'] == $v? ' selected="selected"': null; ?>><?= $name_titles[$v] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="two-third">
                            <label for="fname"><?php __('front_fname'); ?></label>
                            <input type="text" name="fname" id="fname" class="required" value="<?php echo isset($FORM['fname']) ? pjSanitize::clean($FORM['fname']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                    </div>
                    <div class="one-half">
                        <label for="lname"><?php __('front_lname'); ?></label>
                        <input type="text" name="lname" id="lname" class="required" value="<?php echo isset($FORM['lname']) ? pjSanitize::clean($FORM['lname']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                    </div>
                </div>
                <div class="f-row">
                    <div class="one-half">
                        <label for="email"><?php __('front_email'); ?></label>
                        <input type="email" name="email" id="email" class="required" value="<?php echo isset($FORM['email']) ? pjSanitize::clean($FORM['email']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-msg-email="<?php echo pjSanitize::clean(__('front_invalid_email', true, false));?>"/>
                    </div>
                    <div class="one-half">
                        <label for="email2"><?php __('front_confirm_email'); ?></label>
                        <input type="email" name="email2" id="email2" class="required" value="<?php echo isset($FORM['email2']) ? pjSanitize::clean($FORM['email2']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-msg-email="<?php echo pjSanitize::clean(__('front_invalid_email', true, false));?>" data-msg-equalTo="<?php echo pjSanitize::clean(__('front_email_mismatch', true, false));?>"/>
                    </div>
                </div>
                <div class="f-row">
                    <div class="one-half">
                        <label for="trCountryId_<?= $index ?>"><?php __('front_country'); ?></label>
                        <select name="country_id" id="trCountryId_<?= $index ?>" class="select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                            <option value="" data-code=""><?php __('front_choose', false, false); ?></option>
                            <?php foreach($tpl['country_arr'] as $v): ?>
                                <option value="<?= $v['id'] ?>" data-code="<?= $v['code'] ?>" <?php echo (isset($FORM['country_id']) && $FORM['country_id'] == $v['id']) || (!isset($FORM['country_id']) && isset($tpl['default_country_code']) && $tpl['default_country_code'] == $v['alpha_2']) ? ' selected="selected"': null; ?>><?= $v['country_title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php 
					$full_phone_number = '';
					$dialing_code = isset($FORM['dialing_code']) ? pjSanitize::clean($FORM['dialing_code']) : (isset($tpl['default_country_phone']) ? pjSanitize::clean($tpl['default_country_phone']) : null);
					$phone = isset($FORM['phone']) ? pjSanitize::clean($FORM['phone']) : NULL;
					if (!empty($dialing_code) && !empty($phone)) {
						$full_phone_number = $dialing_code.$phone;
					}
					?>
								
                    <div class="one-half">
                        <div class="one-third micro2 micro_2">
                            <label for="trDialingCode_<?= $index ?>"><?php __('front_dialing_code'); ?></label>
                            <input type="text" name="dialing_code" id="trDialingCode_<?= $index ?>" class="required" value="<?php echo $dialing_code;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                        <div class="two-third micro3 micro_3">
                            <label for="phone"><?php __('front_phone'); ?></label>
                            <input type="text" name="phone" id="phone" class="required" value="<?php echo $phone;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn medium color right"><?php __('front_btn_continue'); ?></button>
                </div>
            </form>
        <?php else: ?>
            <div class="trSystemMessage"><?php __('front_error'); ?></div>
        <?php endif; ?>
    </div>

    <?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
</div>