<?php 
/**
 * @file
 * Alpha's theme implementation to display a zone.
 */
?>
<?php if ($wrapper): ?><div<?php print $attributes; ?>><?php endif; ?>  
  <div<?php print $content_attributes; ?>><div class="row">
    <?php print $content; ?>
  </div></div>
<?php if ($wrapper): ?></div><?php endif; ?>