<?php use Roots\Sage\Nav\NavWalker; ?>

<footer class="main" role="contentinfo">
  <div class="container">
    <h2 class="logo"><a href="/">IL Humanities</a></h2>
    <div class="footer-meta">
     <nav role="navigation">
       <?php
       if (has_nav_menu('primary_navigation')) :
         wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker()]);
       endif;
       ?>
     </nav>
     <div class="disclaimer show-for-medium-up">
      <p>Illinois Humanities respects the privacy of its audiences and will at no time sell or distribute personal information to any party not directly affiliated with Illinois Humanities and its programs.</p>
      <p>Illinois Humanities is supported in part by the National Endowment for the Humanities (NEH) and the Illinois General Assembly [through the Illinois Arts Council Agency], as well as by contributions from individuals, foundations and corporations.</p>
      <p>Any views, findings, conclusions, or recommendations expressed by speakers, program participants, or audiences do not necessarily reflect those of the NEH, Illinois Humanities, our partnering organizations or our funders.</p>
     </div>
     <?php
     if (has_nav_menu('footer_links')) :
       wp_nav_menu(['theme_location' => 'footer_links']);
     endif;
     ?>
    </div>
    <ul class="contact-info">
      <li class="address">
        <address class="vcard">
          <span class="org">Illinois Humanities</span><br>
          <a class="underline" target="_blank" href="https://goo.gl/maps/V2HY5ApJVn62"><span class="street-address">Suite 650, 125 South Clark St.</span><br>
            <span class="locality">Chicago, IL</span>
            <span class="postal-code">60603</span>
          </a>
          <br><br><span class="tel">312.422.5580</span> | <span class="tel">312.422.5588</span> FAX<br>
          <a class="email underline" href="mailto:info@ilhumanities.org">info@ilhumanities.org</a>
        </address>
      </li>

      <li class="address">
        <address class="vcard">
          <span class="org">Stony Island Arts Bank</span><br>
          <a class="underline" target="_blank" href="https://goo.gl/maps/2GFzqKC6m5m"><span class="street-address">6760 South Stony Island Ave.</span><br>
            <span class="locality">Chicago, IL</span>
            <span class="postal-code">60649</span>
          </a>
          <br><br><span class="tel">872.244.8603</span><br>
          <a class="email underline" href="mailto:chris.guzaitis@ilhumanities.org">chris.guzaitis@ilhumanities.org</a>
        </address>
      </li>

      <li class="address">
        <address class="vcard">
          <a class="underline" target="_blank" href="https://goo.gl/maps/WFK4JVa9F96dN99j6"><span class="street-address">600 Troy Road<br> N4, Room 207</span><br>
            <span class="locality">Edwardsville, IL</span>
            <span class="postal-code">62025</span>
          </a>
          <br><br><span class="tel">618.468.5580</span><br>
          <a class="email underline" href="mailto:matt.meacham@ilhumanities.org">matt.meacham@ilhumanities.org</a>
        </address>
      </li>

      <li class="social">
        <p>Follow Us:</p>
        <a title="Facebook" target="_blank" href="https://www.facebook.com/<?php echo get_option( 'facebook_id', 'ILhumanities' ); ?>"><span class="icon-facebook"></span></a>
        <a title="Twitter" target="_blank" href="https://twitter.com/<?php echo get_option( 'twitter_id', 'ilhumanities' ); ?>"><span class="icon-twitter"></span></a>
        <a title="Instagram" target="_blank" href="https://instagram.com/<?php echo get_option( 'instagram_id', 'ilhumanities' ); ?>"><span class="icon-instagram"></span></a>
        <a title="YouTube" target="_blank" href="<?php echo get_option( 'youtube_url', 'ilhumanities' ); ?>"><span class="icon-youtube"></span></a>
      </li>

      <li class="source-org copyright">
        &copy; Copyright <?php echo date('Y'); ?> Illinois Humanities
      </li>
    </ul>
  </div>
</footer>
