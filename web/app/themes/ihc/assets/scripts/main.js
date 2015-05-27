// IHC - Firebelly 2015

// GOOD Design for Good Reason for Good Namespace
var IHC = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_huge = false,
      page_cache = [],
      $content,
      $backnav,
      $document,
      map,
      mapFeatureLayer,
      mapGeoJSON = [],
      History = window.History,
      rootUrl = History.getRootUrl(),
      loadingTimer;

  function _init() {
      // Set screen size vars
      _resize();

      $document = $(document);
      $content = $('main');

      // Fit them vids!
      $content.fitVids();

      // Homepage
      if ($('.home.page').length) {
        // Homepage has a funky load-more in events that is part of masonry until clicked
        if (breakpoint_medium) {
          $('.event-cal .events-buttons').clone().addClass('masonry-me').appendTo('.event-cal .events');
        }
      }

      $('<li class="hide-for-medium-up"><a href="#">Disclaimer</a></li>').prependTo('#menu-footer-links').on('click', function(e) {
        e.preventDefault();
        $('.disclaimer').velocity('slideDown');
      });

      // Add class to sidebar image links to target with CSS
      $('.sidebar-content a:has(img)').addClass('img-link');

      // Init behavior for various sections
      _initThoughtSubmit();
      _initSearch();
      _initNav();
      _initMap();
      _initMasonry();
      _initLoadMore();
      _initBigClicky();

      // Esc handlers
      $(document).keyup(function(e) {
        if (e.keyCode === 27) {
          _hideSearch();
          _cancelThoughtSubmit();
        }
      });

      // Add span to accordion titles to style +/- icons
      $('.accordion-title').prepend('<span class="open-status"></span>');

  }

  function _initBigClicky() {
    $(document).on('click', '.article-list article, .focus-list-large article, .bigclicky .flex-item', function(e) {
      if (!$(e.target).is('a')) {
        e.preventDefault();
        var link = $(this).find('h1:first a,h2:first a');
        var href = link.attr('href');
        if (href) { 
          if (e.metaKey || link.attr('target')) {
            window.open(href);
          } else {
            location.href = href; 
          }
        }
      }
    });
  }

  function _scrollBody(element, duration, delay) {
    if ($('#wpadminbar').length) {
      wpOffset = $('#wpadminbar').height();
    } else {
      wpOffset = 0;
    }
    element.velocity("scroll", { 
      duration: duration,
      delay: delay,
      offset: -wpOffset
    }, "easeOutSine");
  } 

  function _initSearch() {
    $('.search-toggle').on('click', function (e) {
      e.preventDefault();
      $('.search-toggle').addClass('search-open');
      $('.search-form').addClass('active');
      $('.search-field').focus();
    });
    $('.search-form .close-button').on('click', _hideSearch);
  }

  function _hideSearch() {
    $('.search-toggle').removeClass('search-open');
    $('.search-form').removeClass('active');
  }

  function _initMap() {
    if ($('#map').length && (breakpoint_medium || $('body.single').length)) {
      L.mapbox.accessToken = 'pk.eyJ1IjoiZmlyZWJlbGx5ZGVzaWduIiwiYSI6IlZMd0JwWWcifQ.k9GG6CFOLrVk7kW75z6ZZA';
      map = L.mapbox.map('map', 'firebellydesign.0238ce0b', { zoomControl: false, attributionControl: false }).setView([41.843, -88.075], 11);
      
      mapFeatureLayer = L.mapbox.featureLayer().addTo(map);

      // Set custom icons
      mapFeatureLayer.on('layeradd', function(e) {
        var marker = e.layer,
          feature = marker.feature;
        marker.setIcon(L.icon(feature.properties.icon));
      });

      _getMapPoints();
    }
  }

  function _getMapPoints() {
    var $mapPoints = $('.map-point:not(.mapped)');
    if ($mapPoints.length) {
      // Any map-points on page? add to map
      $mapPoints.each(function() {
        var $point = $(this).addClass('mapped');
        if ($point.data('lng')) {
          mapGeoJSON.push({
              type: 'Feature',
              geometry: {
                  type: 'Point',
                  coordinates: [ $point.data('lng'), $point.data('lat') ]
              },
              properties: {
                  title: $point.data('title'),
                  description: $point.data('desc'),
                  icon: {
                    "iconUrl": "/app/themes/ihc/dist/images/mapbox/marker.png",
                    "iconSize": [25, 40],
                    "iconAnchor": [12, 40],
                    "popupAnchor": [0, -40],
                    "className": "marker"
                  }
              }
          });
        }
      });
      // Add the array of point objects
      mapFeatureLayer.setGeoJSON(mapGeoJSON);
      // Set bounds to markers
      if ($('#map').hasClass('large')) {
        // Larger map centers on IL
        map.setView([41.7068, -88.3658], 9);
      } else {
        // Smaller map zooms in on single point
        map.fitBounds(mapFeatureLayer.getBounds());
        map.setZoom(6);
      }
    }
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<div class="menu-toggle"><div class="menu-bar"><span class="viz-hide">Menu</span></div></div>')
      .insertAfter('.search-toggle')
      .on('click', function(e) {
        e.preventDefault();
        _toggleMobileMenu();
      });
  }

  function _toggleMobileMenu() {
    $('.menu-toggle').toggleClass('menu-open');
    $('.site-nav').toggleClass('active');
    _hideSearch();
  }

  function _initMasonry(){
    if (breakpoint_medium) {
      $('.masonry').masonry({
        itemSelector: 'article,.masonry-me',
        transitionDuration: '.3s'
      });
    }
  }

  function _initLoadMore() {
    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'news';
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var past_events = (post_type==='events') ? parseInt($load_more.attr('data-past-events')) : 0;
      var focus_area = $load_more.attr('data-focus-area');
      var program = $load_more.attr('data-program');
      var more_container = $load_more.parents('section,main').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      // Homepage has a funky load-more in events that is part of masonry until clicked
      if (breakpoint_medium && $('.home.page').length && $('.events .events-buttons').length) {
        var lm = $('.event-cal').addClass('loaded-more').find('.events .events-buttons');
        // Remove load-more from masonry and relayout
        $('.events').masonry('remove', lm);
        $('.events').masonry();
      }

      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              page: page+1,
              per_page: per_page,
              past_events: past_events,
              focus_area: focus_area,
              program: program
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            more_container.append($data).removeClass('loading');
            if (breakpoint_medium) {
              more_container.masonry('appended', $data, true);
            }
            $load_more.attr('data-page-at', page+1);
            if (post_type==='event') {
              _getMapPoints();
            }

            // Hide load more if last page
            if ($load_more.attr('data-total-pages') <= page + 1) {
                $load_more.addClass('hide');
            }
          }
      });
    });
  }

  function _initThoughtSubmit() {
    $document.on('click', '.submit-thought a', function(e) {
      e.preventDefault();
      if ($('.thought-of-the-day').hasClass('submitting-thought')) {
        _cancelThoughtSubmit();
      } else {
        $('.thought-of-the-day').velocity({opacity: 0, left: -50}, { easing: 'easeInSine', duration: 250,
          complete: function(e) {
            $('.thought-of-the-day').css('left',50).addClass('submitting-thought').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 250 });
          }
        });
      }
    });
    $('.thought-of-the-day .close-button').on('click', _cancelThoughtSubmit);

    // Handle ajax submit of new thought
    $document.on('submit', 'form.new-thought-form', function(e) {
      e.preventDefault();
      var $form = $(this);
      var data = $form.addClass('working').serialize();
      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: data,
          success: function(response) {
            $form.removeClass('working');
            if (response.success) {
              $form.append(response.data.message);
            } else {
              alert(response.data.message);
            }
          }
      });
    });
  }

  function _cancelThoughtSubmit() {
    if ($('.thought-of-the-day').hasClass('submitting-thought')) {
      $('.thought-of-the-day').velocity({opacity: 0, left: 50}, { easing: 'easeInSine', duration: 250,
        complete: function(e) {
          $('.thought-of-the-day').css('left',-50).removeClass('submitting-thought').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 250 });
        }
      });
    }
  }

  // Track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') { ga('send', 'pageview', document.location.href); }
  }

  // Track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { ga('send', 'event', category, action); }
  }

  // Called in quick succession as window is resized
  function _resize() {
    screenWidth = document.documentElement.clientWidth;
    breakpoint_small = (screenWidth > 480);
    breakpoint_medium = (screenWidth > 768);
    breakpoint_large = (screenWidth > 1024);
    breakpoint_huge = (screenWidth > 3000);
  }

  // Called periodically for more intensive resize tasks
  function _delayed_resize() {
    // If (!breakpoint_medium) {
    //   $('.masonry').masonry('destroy');
    // } 
  }

  // Public functions
  return {
    init: _init,
    resize: _resize,
    delayed_resize: _delayed_resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(IHC.init);
// Zig-zag the mothership
jQuery(window).resize(IHC.resize);

jQuery(window).resize(function($){
    // Instant resize functions
    IHC.resize();

    // Delayed resize for more intensive tasks
    if(IHC.delayed_resize_timer) { clearTimeout(IHC.delayed_resize_timer); }
    IHC.delayed_resize_timer = setTimeout(IHC.delayed_resize, 150);
});
