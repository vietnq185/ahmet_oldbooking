<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form']['payment'];
$index = pjObject::escapeString($_GET['index']);
$payment_methods_desc = __('payment_methods_desc', true);
?>
<div class="row">
    <div class="full-width content">
        <header class="f-title color"><?= str_replace('{X}', $STORE['is_return']? 7: 6, __('front_step_x', true, false)) ?><?php __('front_step_payment_details'); ?></header>
        
    </div>

    <div class="three-fourth">
       <?php __('front_discount_code_desc'); ?>
       <br/><br/>
        <?php if($tpl['status'] == 'OK'): 
            $cart = @$tpl['cart'];
            ?>
            <form id="trCheckoutForm_<?php echo $index;?>" action="" method="post">
                <input type="hidden" name="index" value="<?= $index ?>">
                <input type="hidden" name="step_checkout" value="1"/>

                <?php if($tpl['option_arr']['o_payment_disable'] == 'No'): ?>
                	<div class="f-row">
                		<div class="one-half">
                            <label for="voucher_code"><?php __('front_discount_code'); ?></label>
                            <input type="text" name="voucher_code" id="voucher_code" value="<?php echo isset($FORM['voucher_code']) ? pjSanitize::clean($FORM['voucher_code']) : NULL;?>"/>
                        </div>
                	</div>
                	<?php
                	$selected_pm = isset($FORM['payment_method']) && $FORM['payment_method'] ? $FORM['payment_method'] : ''; 
                	$num_pm = 0;
                	$idx = 0;
                	$pm_sort_arr = array('saferpay','paypal','authorize','cash','bank','creditcard_later','creditcard');
                	foreach(__('payment_methods', true, false) as $k => $v) {
                		if($tpl['option_arr']['o_allow_' . $k] == 'Yes') {
                			if ($selected_pm == '' && $idx == 0) {
                				$selected_pm = $k;
                			}
                			$num_pm++;
                			$idx++;
                		}	
                	} 
                	$payment_methods = __('payment_methods', true);
                	?>
                	<div><?php echo sprintf(__('front_payment_method_desc', true), $num_pm); ?></div><br/>
                	<div class="payment-methods">
                		<?php foreach($pm_sort_arr as $k): ?>
							<?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): 
    							$cc_fee = 0;
    							if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) {
    							    $cc_fee = round(($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100);
    							} else if ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) {
    							    $cc_fee = round(($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100);
    							}
							     ?>
								<div class="payment-method">
									<div class="payment-method-info">
										<h3><?php echo @$payment_methods[$k];?>  <?php echo number_format($cart['total'] + $cc_fee, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></h3>
										<div class="payment-method-desc"><?php echo @$payment_methods_desc[$k];?></div>
									</div>
									<div class="payment-method-selector">
										<input type="radio" id="payment_method_<?php echo $k;?>" name="radio_payment_method" class="trPaymentMethodSelector <?php echo $selected_pm == $k ? 'trPaymentMethodSelected' : '';?>" <?php echo $selected_pm == $k ? 'checked="checked"' : '';?> value="<?php echo $k;?>">
									</div>
									<br class="clear-both"/>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
                	</div>
                	
                    <div class="f-row" style="display:none;">
                        <div class="one-half">
                            <label for="payment_method"><?php __('front_payment_medthod'); ?></label>
                            <select name="payment_method" id="trPaymentMethod_<?php echo $index;?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <option value="" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>"><?php __('front_choose', false, false); ?></option>
                                <?php foreach(__('payment_methods', true, false) as $k => $v) { ?>
                                    <?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes') { ?>
                                    	<?php if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format(round(($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($cart['deposit'] + round((($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'] + round((($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
                                    	<?php } elseif ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format(round(($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($cart['deposit'] + round((($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'] + round((($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
                                    	<?php } else { ?>
                                        	<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="" data-deposit="<?php echo number_format($cart['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
                                        <?php } ?>
                                    <?php }  ?>
                                <?php } ?>
                                
                            </select>
                        </div>
                    </div>
                    
                    <div id="trCCData_<?php echo $index;?>" style="display: <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'block' : 'none'; ?>">
                        <div class="f-row">
                            <div class="one-half">
                                <label for="cc_owner"><?php __('front_cc_owner'); ?></label>
                                <input type="text" name="cc_owner" class="required" value="<?php echo isset($FORM['cc_owner']) ? pjSanitize::clean($FORM['cc_owner']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                            </div>

                            <div class="one-half">
                                <label for="cc_num"><?php __('front_cc_num'); ?></label>
                                <input type="text" name="cc_num" class="required" value="<?php echo isset($FORM['cc_num']) ? pjSanitize::clean($FORM['cc_num']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                            </div>
                        </div>

                        <div class="f-row">
                            <div class="one-fourth">
                                <label for="cc_exp_month"><?php __('front_cc_exp'); ?> (<?php __('front_month'); ?>)</label>
                                <select name="cc_exp_month" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_exp_month', true, false));?>">
                                    <option value="">-- <?php __('front_choose', false, false); ?> --</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"<?php echo (int) @$FORM['cc_exp_month'] == $i ? ' selected="selected"' : NULL; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="one-fourth">
                                <label for="cc_exp_year"><?php __('front_cc_exp'); ?> (<?php __('front_year'); ?>)</label>
                                <select name="cc_exp_year" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_exp_year', true, false));?>">
                                    <option value="">-- <?php __('front_choose', false, false); ?> --</option>
                                    <?php $y = (int) date('Y'); ?>
                                    <?php for($i = $y; $i <= $y + 10; $i++): ?>
                                        <option value="<?= $i ?>"<?php echo @$FORM['cc_exp_year'] == $i ? ' selected="selected"' : NULL; ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="one-sixth">
                                <label for="cc_code"><?php __('front_cc_code'); ?></label>
                                <input type="text" name="cc_code" class="required" value="<?php echo isset($FORM['cc_code']) ? pjSanitize::clean($FORM['cc_code']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                            </div>
                        </div>

                        <?php if($tpl['option_arr']['o_deposit_payment'] > 0): ?>
                            <div class="f-row">
                                <?= str_replace('{X}', (float) $tpl['option_arr']['o_deposit_payment'], __('front_deposit_payment_in_advance', true)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>


                <div class="f-row">
                    <br />
                    <label for="agreement"><?php __('front_agree'); ?></label>
                    <input id="trAgree_<?php echo $index?>" name="agreement" type="checkbox" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                    <?php if(!empty($tpl['terms_conditions'])): ?>
                        &nbsp;<a href="#" id="trBtnTerms_<?php echo $index?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </div>

                <?php if(!empty($tpl['terms_conditions'])): ?>
                    <div id="trTermContainer_<?php echo $index;?>"  class="f-row" style="display: none;">
                        <p><?php echo $tpl['terms_conditions'];?></p>
                    </div>
                <?php endif; ?>
                <div id="trBookingMsg_<?php echo $index?>" class="f-row" style="display: none;">
                    <div class="alert"></div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn medium color right btnBook" data-html_book="<?php __('front_btn_book_now'); ?>" data-html_book_pay="<?php __('front_btn_book_and_pay'); ?>"><?php echo $selected_pm == 'saferpay' ? __('front_btn_book_now', true) : __('front_btn_book_and_pay', true); ?></button>
                </div>
            </form>
        <?php else: ?>
            <div class="trSystemMessage">
                <?php __('front_error'); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
</div>