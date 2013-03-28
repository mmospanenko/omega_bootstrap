<?php if ($wrapper): ?><div<?php print $attributes; ?>><?php endif; ?>
  <div<?php print $content_attributes; ?>><div class="row">
    <?php if ($breadcrumb): ?>
      <div id="breadcrumb" class="grid-<?php print $columns; ?>"><?php print $breadcrumb; ?></div>
    <?php endif; ?>    
    <?php if ($messages): ?>
      <div id="messages" class="grid-<?php print $columns; ?>"><?php print $messages; ?></div>
    <?php endif; ?>
    <?php print $content; ?>
  </div></div>
<?php if ($wrapper): ?></div><?php endif; ?>