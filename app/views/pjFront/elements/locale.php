<div class="trLocale">
<?php
if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']))
{
	if(count($tpl['locale_arr']) > 1)
	{
		?>
		<ul class="trLocaleMenu"><?php
		$locale_id = $controller->pjActionGetLocale();
		foreach ($tpl['locale_arr'] as $locale)
		{
			?><li><a href="#" class="trSelectorLocale<?php echo $locale_id == $locale['id'] ? ' trLocaleFocus' : NULL; ?>" data-id="<?php echo $locale['id']; ?>" title="<?php echo pjSanitize::html($locale['title']); ?>"><img src="<?php echo PJ_INSTALL_URL . 'core/framework/libs/pj/img/flags/' . $locale['file'] ?>" alt="<?php echo htmlspecialchars($locale['title']); ?>" /></a></li><?php
		}
		?>
		</ul>
		<?php
	}
}
?>
</div>