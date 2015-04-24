<div class="search-toggle">
  <div class="icon-search"></div>
</div>
<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
  <div class="search-close"></div>
  <label class="sr-only"><?php _e('Search for:', 'sage'); ?></label>
  <div class="input-group">
    <input type="search" value="" autocomplete="off" name="s" class="search-field form-control" placeholder="Search" required>
            <button type="submit" class="search-submit icon-search">
            <span class="viz-hide"><?php _e('Search', 'sage'); ?></span>
        </span>
      </div>
    </div>
</form>
