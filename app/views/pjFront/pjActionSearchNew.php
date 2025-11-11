<?php
    $index = pjObject::escapeString($_GET['index']);
?>

<main class="main" role="main" id="trContainer_<?php echo $index;?>">
    <div class="advanced-search">
        <div class="wrap">
            
            <?php include 'elements/locale.php'; ?>
            <div class="clearfix"></div>

            <form id="trSearchForm_<?php echo $index;?>" action="" method="post">
                <input type="hidden" name="index" value="<?= $index ?>">
                <input type="hidden" name="autoload_next_step" id="autoloadNextStep_<?php echo $index;?>" value="<?= (int) @$tpl['search_post']['autoload_next_step']; ?>"/>
                <input type="hidden" name="skip_first_step" value="1">
                <!-- Row -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group select">
                            <i class="icon-origin"></i>
                            <select name="search_location_id" id="trLocationId_<?php echo $index;?>" class="select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-placeholder="<?php __('lblStartingPoint', false, false); ?>">
                                <option value=""></option>
                                <?php
                                    foreach($tpl['pickup_arr'] as $k => $v)
                                    {
                                        ?><option
                                            value="<?php echo $v['id'];?>"
                                            <?php echo isset($tpl['search_post']) ? ($tpl['search_post']['location_id'] == $v['id'] ? ' selected="selected"' : null) : null;?>
                                            data-icon="image-<?php echo $v['icon']; ?>"
                                        ><?php echo $v['pickup_location'];?></option><?php
                                    }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group select">
                            <i class="icon-destination"></i>
                            <span id="dropoffBox_<?php echo $index;?>" class="trContent">
                                <select name="search_dropoff_id" id="trDropoffId_<?php echo $index;?>" class="select2 required trLoadPrices" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-placeholder="<?php __('lblYourDestination', false, false); ?>">
                                    <option value=""></option>
                                    <?php
                                        if(isset($tpl['search_post']))
                                        {
                                            foreach($tpl['dropoff_arr'] as $k => $v)
                                            {
                                                ?><option value="<?php echo $v['id'];?>"<?php echo isset($tpl['search_post']) ? ($tpl['search_post']['dropoff_id'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php echo $v['location'];?></option><?php
                                            }
                                        }
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group select">
                            <i class="fa fa-user-o"></i>
                            <select name="search_passengers_from_to" class="required trLoadPrices" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <?php /*<option value=""><?php __('front_passengers', false, false); ?></option> */?>
                                <?php foreach($tpl['passenger_range_arr'] as $v): ?>
                                    <option value="<?= $v ?>"<?php echo isset($tpl['search_post']) ? ($tpl['search_post']['passengers_from_to'] == $v ? ' selected="selected"' : null) : null;?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group datepicker">
                            <i class="icon-datepicker"></i>
                            <input type="text" placeholder="<?php __('lblYourDate', false, true); ?>" id="trDate_<?php echo $index?>" name="search_date" readonly value="<?php echo isset($tpl['search_post']) && isset($tpl['search_post']['date']) ? htmlspecialchars($tpl['search_post']['date']) : null; ?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <button type="submit" class="btn large black">
                                <i class="icon-search"></i>
                                <span><?php __('lblSeePrices'); ?></span>
                            </button>
                            <p class="trCheckErrorMsg"></p>
                        </div>
                    </div>
                </div>
                <!--// Row -->
            </form>
        </div>
    </div>
</main>