<?php
$STORE = @$tpl['store'];
$cartIndex = str_replace('pjAction', '', $_GET['action']) . '_' . pjObject::escapeString($_GET['index']);
$cart = @$tpl['cart'];
?>
<aside id="trCart_<?php echo $cartIndex;?>" class="one-fourth sidebar right">
    <div class="widget">
        <h4><?php __('front_booking_summary'); ?></h4>
        <div class="summary">
            <div>
                <h5><?php __('front_cart_departure'); ?></h5>
                <dl>
                    <dt class="trCartDepartureDate"><?php __('front_date'); ?></dt>
                    <dd class="trCartDepartureDate"><?= $cart['date'] ?></dd>

                    <?php if($_GET['action'] != 'pjActionExtras'): ?>
                        <dt class="trCartDepartureTime" style="display: <?= !empty($cart['time'])? 'block': 'none' ?>;"><?php __('front_time'); ?></dt>
                        <dd class="trCartDepartureTime" style="display: <?= !empty($cart['time'])? 'block': 'none' ?>;"><?= $cart['time'] ?></dd>
                    <?php endif; ?>

                    <dt><?php __('front_cart_from'); ?></dt>
                    <dd><?= $cart['pickup_location_name'] ?></dd>

                    <dt><?php __('front_cart_to'); ?></dt>
                    <dd><?= $cart['dropoff_location_name'] ?></dd>

                    <dt class="trCartPax" style="display: <?= !empty($cart['passengers'])? 'block': 'none' ?>;"><?php __('front_cart_passengers'); ?></dt>
                    <dd class="trCartPax" style="display: <?= !empty($cart['passengers'])? 'block': 'none' ?>;"><?= $cart['passengers'] ?></dd>

                    <dt><?php __('front_vehicle'); ?></dt>
                    <dd><?= $cart['fleet'] ?></dd>

                    <dt class="trCartExtras" style="display: <?= !empty($cart['extras'])? 'block': 'none' ?>;"><?php __('front_extras'); ?></dt>
                    <dd class="trCartExtras" style="display: <?= !empty($cart['extras'])? 'block': 'none' ?>;"><?= $cart['extras'] ?></dd>
                </dl>
            </div>

            <?php if($cart['is_return']): ?>
                <div>
                    <h5><?php __('front_cart_return'); ?></h5>
                    <dl>
                        <?php if(!in_array($_GET['action'], array('pjActionExtras', 'pjActionDeparture'))): ?>
                            <dt class="trCartReturnDate" style="display: <?= !empty($cart['return_date'])? 'block': 'none' ?>;"><?php __('front_date'); ?></dt>
                            <dd class="trCartReturnDate" style="display: <?= !empty($cart['return_date'])? 'block': 'none' ?>;"><?= $cart['return_date'] ?></dd>

                            <dt class="trCartReturnTime" style="display: <?= !empty($cart['return_time'])? 'block': 'none' ?>;"><?php __('front_time'); ?></dt>
                            <dd class="trCartReturnTime" style="display: <?= !empty($cart['return_time'])? 'block': 'none' ?>;"><?= $cart['return_time'] ?></dd>
                        <?php endif; ?>

                        <dt><?php __('front_cart_from'); ?></dt>
                        <dd><?= $cart['dropoff_location_name'] ?></dd>
                        <dt><?php __('front_cart_to'); ?></dt>
                        <dd><?= $cart['pickup_location_name'] ?></dd>
                    </dl>
                </div>
            <?php endif; ?>

            <?php if(in_array($_GET['action'], array('pjActionCheckout'))): 
                $payment_methods = __('payment_methods', true);
                $deposit = $cart['total'] * $tpl['option_arr']['o_deposit_payment'] / 100;
                $rest = $cart['total'] - $deposit;
                ?>
                <div>
                	<h5><?php __('front_payment'); ?></h5>
                    <dl>
                        <span class="trCartPaymentMethod_<?php echo $cartIndex;?> pjSbCartPaymentMethod"><?php echo @$payment_methods[$cart['payment_method']]; ?></span>
                        
                        <?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
                			<span class="pjSbFullPriceChargedDesc" style="display: <?php echo in_array(@$cart['payment_method'], array('saferpay', 'creditcard')) ? '' : 'none';?>">
	                            <br/><?php __('front_now_to_pay'); ?>: <span class="pjSbCartDeposit"><?php echo number_format($cart['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
	                            <br/><?php __('front_rest_to_pay'); ?>: <span class="pjSbCartRest"><?php echo number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
                            </span>
                        <?php } ?>
                        
                        <dt class="trCartDiscount_<?php echo $cartIndex;?>" style="display: <?= !empty($cart['discount'])? 'block': 'none' ?>;"><?php __('front_discount'); ?></dt>
                        <dd class="trCartDiscount_<?php echo $cartIndex;?>" style="display: <?= !empty($cart['discount'])? 'block': 'none' ?>;"><?= number_format($cart['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
                        <?php /*if($tpl['option_arr']['o_deposit_payment'] > 0): ?>
                            <?php
                            $display = isset($cart['payment_method']) && $cart['payment_method'] == 'creditcard' && !empty($cart['deposit'])? 'block': 'none';
                            ?>
                            <dt class="trCartDeposit_<?php echo $cartIndex;?>" style="display: <?= $display ?>;"><?php __('front_deposit'); ?></dt>
                            <dd class="trCartDeposit_<?php echo $cartIndex;?>" style="display: <?= $display ?>;"><?= number_format($cart['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
                            <dt class="trCartRest_<?php echo $cartIndex;?>" style="display: <?= $display ?>;"><?php __('front_rest'); ?></dt>
                            <dd class="trCartRest_<?php echo $cartIndex;?>" style="display: <?= $display ?>;"><?= number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
                        <?php endif;*/ ?>
                    </dl>
                </div>
            <?php endif; ?>

            <dl class="total pjSbTotalPrice">
                <dt><?php __('front_total'); ?></dt>
                <dd>
                	<span class="pjSbCartTotal"><?= number_format($cart['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></span>
                	<small><?php __('front_total_price_all_inclusive');?></small>
                </dd>
            </dl>
        </div>
    </div>
</aside>