// IHC - Firebelly 2015

// good design for good reason for good namespace
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
      // set screen size vars
      _resize();

      $document = $(document);
      $content = $('section.main');

      // fit them vids!
      $content.fitVids();

      // homepage
      if ($('.home.page').length) {
        // duplicate thought of the day for mobile
        var mobileThought = $('.thought-of-the-day').addClass('show-for-medium-up').clone();
        mobileThought.removeClass('show-for-medium-up').addClass('hide-for-medium-up').appendTo('.content');

        // homepage has a funky load-more in events that is part of masonry until clicked
        if (breakpoint_medium) {
          $('.event-cal .events-buttons').clone().addClass('masonry-me').appendTo('.event-cal .events');
        }
      }

      // init behavior for various sections
      _initThoughtSubmit();
      _initSearch();
      _initNav();
      _initMap();
      _initSliders();
      _initMasonry();
      _initLoadMore();
      _initBigClicky();

      // esc handlers
      $(document).keyup(function(e) {
        if (e.keyCode === 27) {
          _hideSearch();
        }
      });

  }

  function _initBigClicky() {
    $(document).on('click', '.article-list article', function(e) {
      if (!$(e.target).is('a')) {
        e.preventDefault();
        var href = $(this).find('h1:first a').attr('href');
        if (href) { location.href = href; }
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
    $('.search-close').on('click', _hideSearch);
  }

  function _hideSearch() {
    $('.search-toggle').removeClass('search-open');
    $('.search-form').removeClass('active');
  }

  function _initMap() {
    if ($('#map').length) {
      L.mapbox.accessToken = 'pk.eyJ1IjoiZmlyZWJlbGx5ZGVzaWduIiwiYSI6IlZMd0JwWWcifQ.k9GG6CFOLrVk7kW75z6ZZA';
      map = L.mapbox.map('map', 'firebellydesign.lkh3a3i1').setView([41.843, -88.075], 11);
      
      mapFeatureLayer = L.mapbox.featureLayer().addTo(map);

      // set custom icons
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
      // any map-points on page? add to map
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
      // add the array of point objects
      mapFeatureLayer.setGeoJSON(mapGeoJSON);
      // set bounds to markers
      map.fitBounds(mapFeatureLayer.getBounds());
    }
  }

  // handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<div class="menu-toggle"><div class="menu-bar"><span class="viz-hide">Menu</span></div></div>')
      .prependTo('body')
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

  function _initSliders(){
    $('.slider').slick({
      slide: '.slide-item',
      // autoplay: $('.home.page').length>0,
      autoplaySpeed: 8000,
      speed: 800,
      appendArrows: $('.slide-wrap-inner'),
      prevArrow:  '<svg class="slick-prev icon icon-arrow-left" role="img"><use xlink:href="#icon-arrow-left"></use></svg>',
      nextArrow: '<svg class="slick-next icon icon-arrow-right" role="img"><use xlink:href="#icon-arrow-right"></use></svg>'
    });
  }

  function _initFaq(){
    $('.faq-answer').velocity('slideUp', { duration: 0 });
    $document.on('click', '.faq-nav a', function(e) {
      e.preventDefault();
      var $this = $(this);
      if ($this.closest('li').hasClass('active')) { return false; }
      _showFaq($this.attr('href'), 1);
    });
    
    // check if we're linking to #faq2 (not currently used)
    if (location.hash !== '' && location.hash.match(/faq/)) {
      _showFaq(location.hash);
      // scroll to FAQ section
      _scrollBody($('.faq'), 250, 0);
    }
    if ($('.faq-nav li.active').length === 0) {
      // make first FAQ active if none selected
      _showFaq($('.faq-nav li:first a').attr('href'));
    }
  }

  function _showFaq(faq, update_url) {
    $('.faq-nav li').removeClass('active');
    $('.faq-nav li a[href="'+faq+'"]').closest('li').addClass('active');

    var faq_answer = $(faq + '.faq-answer');

    $('.faq-answer.active').velocity("slideUp", { duration: 150 });
    faq_answer.addClass('active').velocity("slideDown", { delay: 200, duration: 400 });
    if (typeof update_url !== 'undefined') {
      History.replaceState({}, null, faq);
    }
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
      var more_container = $load_more.parents('section').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      // homepage has a funky load-more in events that is part of masonry until clicked
      if (breakpoint_medium && $('.home.page').length && $('.events .events-buttons').length) {
        var lm = $('.event-cal').addClass('loaded-more').find('.events .events-buttons');
        // remove load-more from masonry and relayout
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

            // hide load more if last page
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
      $('.thought-of-the-day').addClass('submitting-thought');
    });
    // handle ajax submit of new thought
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

  // track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') { ga('send', 'pageview', document.location.href); }
  }

  // track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { ga('send', 'event', category, action); }
  }

  // called in quick succession as window is resized
  function _resize() {
    screenWidth = document.documentElement.clientWidth;
    breakpoint_small = (screenWidth > 480);
    breakpoint_medium = (screenWidth > 768);
    breakpoint_large = (screenWidth > 1024);
    breakpoint_huge = (screenWidth > 3000);
  }

  // called periodically for more intensive resize tasks
  function _delayed_resize() {
    // if (!breakpoint_medium) {
    //   $('.masonry').masonry('destroy');
    // } 
  }

  // public functions
  return {
    init: _init,
    resize: _resize,
    delayed_resize: _delayed_resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    }
  };

})(jQuery);

// fire up the mothership
jQuery(document).ready(IHC.init);
// zig-zag the mothership
jQuery(window).resize(IHC.resize);

jQuery(window).resize(function($){
    // instant resize functions
    IHC.resize();

    // delayed resize for more intensive tasks
    if(IHC.delayed_resize_timer) { clearTimeout(IHC.delayed_resize_timer); }
    IHC.delayed_resize_timer = setTimeout(IHC.delayed_resize, 150);
});


(function($){
  // internal helper (from Ajaxify)
  $.expr[':'].internal = function(obj, index, meta, stack){
    var
      $this = $(obj),
      url = $this.attr('href')||'',
      isInternalLink,
      rootUrl = History.getRootUrl();
    isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;
    return isInternalLink;
  };
})(jQuery);