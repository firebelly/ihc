<?php use Roots\Sage\Nav\NavWalker; ?>

<footer class="main" role="contentinfo">
  <div class="container">
    <div class="contact-info">
      <li class="address">
        <address class="vcard"> 
          <span class="org">Illinois Humanities</span><br>
          <span class="street-address">Suite 1400, 17 North State St.</span><br>
          <span class="locality">Chicago, IL</span>
          <span class="postal-code">60602-3296</span>
        </address>
      </li>

      <li class="contact">
        <span class="tel">312.422.5580</span> | <span class="tel">312.422.5580</span> FAX<br>
        <a class="email" href="mailto:info@illinoishumanities.org">info@illinoishumanities.org</a>
      </li>

      <li class="social">
        <a target="_blank" class="facebook" href="https://www.facebook.com/<?php echo get_option( 'facebook_id', 'ILhumanities' ); ?>"><i>Facebook</i><span class="icon-facebook"></span></a>
        <a target="_blank" class="twitter" href="https://twitter.com/<?php echo get_option( 'twitter_id', 'ilhumanities' ); ?>"><i>Twitter</i><span class="icon-twitter"></span></a>
        <a target="_blank" class="twitter" href="https://instagram.com/<?php echo get_option( 'instagram_id', 'ilhumanities' ); ?>"><i>Instagram</i><span class="icon-instagram"></span></a>
      </li>

      <li class="source-org copyright">
        &copy; Copyright <?php echo date('Y'); ?> Illinois Humanities Council
      </li>
    </div>
    
    <div class="footer-meta">
     <nav role="navigation">
       <?php
       if (has_nav_menu('primary_navigation')) :
         wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker()]);
       endif;
       ?>
     </nav>

     <div class="disclaimer">
       <p>Illinois Humanities respects the privacy of its audiences and will at no time sell or distribute personal information to any party not directly affiliated with Illinois Humanities and its programs.</p>
       <p>Illinois Humanities is supported in part by the National Endowment for the Humanities (NEH) and the Illinois General Assembly [through the Illinois Arts Council, a state agency], as well as by contributions from individuals, foundations and corporations.</p>
       <p>Any views, findings, conclusions, or recommendations expressed by speakers, program participants, or audiences do not necessarily reflect those of the NEH, the Illinois Humanities, our partnering organizations or our funders.</p>
     </div>

     <?php
     if (has_nav_menu('footer_links')) :
       wp_nav_menu(['theme_location' => 'footer_links']);
     endif;
     ?>
    </div>
  </div>
</footer>
