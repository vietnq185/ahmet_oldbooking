<?php $index = pjObject::escapeString($_GET['index']); ?>
<select name="dropoff_id" id="trDropoffId_<?php echo $index;?>" class="trLoadPrices required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
    <option value="">-- <?php __('front_choose', false, false); ?> --</option>
    <?php
    foreach($tpl['dropoff_arr'] as $k => $v)
    {
        ?><option value="<?php echo $v['id'];?>" data-icon="image-<?php echo $v['icon']; ?>"><?php echo $v['location'];?></option><?php
    }
    ?>
</select>