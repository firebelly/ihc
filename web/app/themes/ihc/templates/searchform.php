<div class="search-toggle icon-search"></div><form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
<div class="search-close"></div>
  <label class="sr-only"><?php _e('Search for:', 'sage'); ?></label>
  <div class="input-group">
	<input type="search" value="<?= get_search_query(); ?>" name="s" class="search-field form-control" placeholder="Search" required>
    	<span class="input-group-btn">
      	<button type="submit" class="search-submit icon-search"><span class="viz-hide"><?php _e('Search', 'sage'); ?></span></button>
    	</span>
    </div>
  </div>
</form>
