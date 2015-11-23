<!doctype html>
<!--[if IE 8]> <html class="no-js ie8 lt-ie9 lt-ie10" lang="en"> <![endif]-->
<!--[if IE 9 ]> <html class="no-js ie9 lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="alternate" type="application/rss+xml" title="<?= get_bloginfo('name'); ?> Feed" href="<?= esc_url(get_feed_link()); ?>">
    <link rel="icon" type="image/png" href="<?= Roots\Sage\Assets\asset_path('images/favicon-32x32.png') ?>" />
    <script>
      (function(d) {
        var config = {
          kitId: 'ekm2caz',
          scriptTimeout: 3000
        },
        h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
      })(document);
    </script>
    
    <?php wp_head(); ?>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-63659858-1', 'auto');

      <?php 
      // Post Category Analytics custom dimension
      if (is_single()):
        if (in_category('blog-article')): ?>
          ga('set', 'dimension1', 'blog');
        <?php else: ?>
          ga('set', 'dimension1', '<?= $post->post_type != 'post' ? $post->post_type : 'news' ?>');
        <?php endif; ?>

      <?php elseif (is_home()): ?>
          ga('set', 'dimension1', 'news');
      <?php elseif (is_post_type_archive(['event'])): ?>
        ga('set', 'dimension1', 'event');
      <?php elseif (is_post_type_archive(['program'])): ?>
        ga('set', 'dimension1', 'program');
      <?php elseif (is_tax('focus_area')): ?>
        ga('set', 'dimension1', 'focus_area');
      <?php elseif (is_archive()): ?>
        ga('set', 'dimension1', <?= is_category('blog-article') ? 'blog' : 'news' ?>);
      <?php endif; ?>

      ga('send', 'pageview');
    </script>
  </head>
