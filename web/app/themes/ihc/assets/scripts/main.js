// IL Humanities - Firebelly 2015
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var IHC = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,1000,1200],
      $document,
      $sidebar,
      $tod,
      no_header_text,
      map,
      mapFeatureLayer,
      mapGeoJSON = [],
      mapIconRed,
      mapIconBlue,
      mapTop,
      loadingTimer,
      page_at;

  function _init() {
    // touch-friendly fast clicks
    FastClick.attach(document.body);

    // Cache some common DOM queries
    $document = $(document);
    $('body').addClass('loaded');
    $sidebar = $('aside.main');
    $tod = $('section.thought-of-the-day');
    no_header_text = $('header.no-header-text').length;

    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

    // Homepage (pre _initMasonry)
    if ($('.home.page').length) {
      page_at = 'homepage';
      // Homepage has a funky load-more in events that is part of masonry until clicked
      if (breakpoint_medium) {
        $('.event-cal .events-buttons').clone().addClass('masonry-me').appendTo('.event-cal .events');
      }
    }

    // Disclaimer mobile link that reveals hidden disclaimer block
    $('<li class="hide-for-medium-up"><a href="#">Disclaimer</a></li>').prependTo('#menu-footer-links').on('click', function(e) {
      e.preventDefault();
      $('.disclaimer').velocity('slideDown');
    });

    // Add .img-link class to sidebar image links to target with CSS
    $('.sidebar-content a,.user-content a,.event-details a').has('img').addClass('img-link');

    // Init behavior for various sections
    _initThoughtSubmit();
    _initNav();
    _initSearch();
    // _initMap();
    _initMasonry();
    _initLoadMore();
    _initBigClicky();
    _initFilters();

    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        _hideSearch();
        _cancelThoughtSubmit();
        _hideMobileNav();
      }
    });

    // Add span to accordion titles to style +/- icons
    $('.accordion-title').prepend('<span class="open-status"></span>');

    // Events landing page
    if ($('.post-type-archive-event').length) {
      if (breakpoint_medium) {
        // Set initial mapTop position
        mapTop = $('#map').offset().top;
        // Onscroll toggle sticky class on large map
        $(window).on('scroll', _scroll);
      }
    }

    // Run resize again in case anything needs adjusting
    _resize();

    // Smoothscroll links
    $('a.smoothscroll').click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      _scrollBody($(href));
    });

    // Scroll down to hash afer page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash), 500, 250);
      }
    });

  } // end _init()

  function _initFilters() {
    $('form.filters').on('submit', function(e) {
      if ($('form.filters select[name=prox_miles]').length) {
        if ($('form.filters select[name=prox_miles]').val() !== '' && $('form.filters input[name=prox_zip]').val() === '') {
          e.preventDefault();
          alert('Please enter a zip code.');
          $('form.filters input[name=prox_zip]')[0].focus();
        } else if ($('form.filters select[name=prox_miles]').val() === '' && $('form.filters input[name=prox_zip]').val() !== '') {
          $('form.filters select[name=prox_miles]').val(1);
        }
      }
    });
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
    var wpOffset = 80;
    if ($('#wpadminbar').length) {
      wpOffset += $('#wpadminbar').height();
    }

    // defaults
    if (typeof duration === 'undefined') {
      duration = 500;
    }
    if (typeof delay === 'undefined') {
      duration = 0;
    }
    element.velocity('scroll', {
      duration: duration,
      delay: delay,
      offset: -wpOffset
    }, 'easeOutSine');
  }

  function _initSearch() {
    $('.search-form:not(.mobile-search) .search-submit').on('click', function (e) {
      if ($('.search-form').hasClass('active')) {

      } else {
        e.preventDefault();
        $('.search-form').addClass('active');
        $('.search-field:first').focus();
      }
    });
    $('.search-form .close-button').on('click', function() {
      _hideSearch();
      _hideMobileNav();
    });
  }

  function _hideSearch() {
    $('.search-form').removeClass('active');
  }

  function _initMap() {
    // Only init Mapbox if > breakpoint_medium, or on a body.single page (small sidebar maps)
    if ($('#map').length && (breakpoint_medium || $('body.single').length)) {
      L.mapbox.accessToken = 'pk.eyJ1IjoiZmlyZWJlbGx5ZGVzaWduIiwiYSI6IlZMd0JwWWcifQ.k9GG6CFOLrVk7kW75z6ZZA';
      map = L.mapbox.map('map', 'firebellydesign.0238ce0b', { zoomControl: false, attributionControl: false }).setView([41.843, -88.075], 11);

      mapFeatureLayer = L.mapbox.featureLayer().addTo(map);

      mapIconRed = L.icon({
        iconUrl: "/app/themes/ihc/dist/images/mapbox/marker-red.png",
        iconSize: [25, 42],
        iconAnchor: [12, 40],
        popupAnchor: [0, -40],
        className: "marker-red"
      });
      mapIconBlue = L.icon({
        iconUrl: "/app/themes/ihc/dist/images/mapbox/marker-blue.png",
        iconSize: [25, 42],
        iconAnchor: [12, 40],
        popupAnchor: [0, -40],
        className: "marker-blue"
      });

      // Set custom icons
      mapFeatureLayer.on('layeradd', function(e) {
        var marker = e.layer,
          feature = marker.feature;
        marker.setIcon(feature.properties.icon);
      });

      // Larger map behavior
      if ($('#map').hasClass('large')) {
        // Disable zoom/scroll
        map.dragging.disable();
        map.touchZoom.disable();
        map.doubleClickZoom.disable();
        map.scrollWheelZoom.disable();

        // Prevent the listeners from disabling default
        // actions (http://bingbots.com/questions/1428306/mapbox-scroll-page-on-touch)
        L.DomEvent.preventDefault = function(e) {return;};

        // Click to open event
        mapFeatureLayer.on('click', function(e) {
          e.layer.closePopup();
          var event_url = e.layer.feature.properties.event_url;
          location.href = event_url;
        });

        // Hover events to highlight listings
        mapFeatureLayer.on('mouseover', function(e) {
          // e.layer.openPopup();
          var event_id = e.layer.feature.properties.event_id;
          var article = $('.events article[data-id='+event_id+']');
          if (article.length) {
            article.addClass('hover');
          }
          _highlightMapPoint(event_id);
        });
        mapFeatureLayer.on('mouseout', function(e) {
          e.layer.closePopup();
          var event_id = e.layer.feature.properties.event_id;
          var article = $('.events article[data-id='+event_id+']');
          if (article.length) {
            article.removeClass('hover');
          }
          _unHighlightMapPoints();
        });
      } else {
        // Smaller maps need no tooltip
        mapFeatureLayer.on('click', function(e) {
          e.layer.closePopup();
        });
      }

      _getMapPoints();
    }
  }

  function _getMapPoints() {
    var $mapPoints = $('.map-point:not(.mapped)');
    if ($mapPoints.length) {
      // Any map-points on page? add to map
      $mapPoints.each(function() {
        var event_id = $(this).attr('data-id');
        var $point = $(this).addClass('mapped').hover(function() {
          _highlightMapPoint(event_id);
        }, _unHighlightMapPoints);
        if ($point.attr('data-lng')) {
          mapGeoJSON.push({
              type: 'Feature',
              geometry: {
                  type: 'Point',
                  coordinates: [ $point.attr('data-lng'), $point.attr('data-lat') ]
              },
              properties: {
                  title: $point.attr('data-title'),
                  event_id: $point.attr('data-id'),
                  event_url: $point.attr('data-url'),
                  description: $point.attr('data-desc'),
                  icon: mapIconRed
              }
          });
        }
      });
      // Add the array of point objects
      mapFeatureLayer.setGeoJSON(mapGeoJSON);

      if ($('#map').hasClass('large')) {
        // Larger map centers on IL
        map.setView([39.9, -90.5], 7);
      } else {
        // Smaller map zooms in on single point
        if ($mapPoints.first().attr('data-lng')) {
          map.setView([$mapPoints.first().attr('data-lat'), $mapPoints.first().attr('data-lng')], 13);
        }
      }
    }
  }

  function _highlightMapPoint(event_id) {
    mapFeatureLayer.eachLayer(function(marker) {
      if (marker.feature.properties.event_id === event_id) {
        marker.setIcon(mapIconRed);
        marker.setZIndexOffset(1000);
      } else {
        marker.setIcon(mapIconBlue);
        marker.setZIndexOffset(0);
      }
    });
    // mapFeatureLayer.setGeoJSON(mapGeoJSON);
  }
  function _unHighlightMapPoints() {
    mapFeatureLayer.eachLayer(function(marker) {
      marker.setIcon(mapIconRed);
      marker.setZIndexOffset(0);
    });
    // mapFeatureLayer.setGeoJSON(mapGeoJSON);
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<div class="menu-toggle"><div class="menu-bar"><span class="sr-only">Menu</span></div></div>')
      .prependTo('header.banner')
      .on('click', function(e) {
        _showMobileNav();
      });
    var mobileSearch = $('.search-form').clone().addClass('mobile-search');
    mobileSearch.prependTo('.site-nav');
  }

  function _showMobileNav() {
    $('.menu-toggle').addClass('menu-open');
    $('.site-nav').addClass('active');
  }

  function _hideMobileNav() {
    $('.menu-toggle').removeClass('menu-open');
    $('.site-nav').removeClass('active');
  }

  function _initMasonry(){
    if (breakpoint_medium) {
      $('.masonry').masonry({
        itemSelector: 'article,.masonry-me',
        transitionDuration: '.35s'
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
      var past_events = (post_type==='event') ? parseInt($load_more.attr('data-past-events')) : 0;
      var prox_zip = (post_type==='event') ? parseInt($load_more.attr('data-prox-zip')) : '';
      var prox_miles = (post_type==='event') ? parseInt($load_more.attr('data-prox-miles')) : '';
      var focus_area = $load_more.attr('data-focus-area');
      var division = $load_more.attr('data-division');
      var program = $load_more.attr('data-program');
      var exhibitions = $load_more.attr('data-exhibitions');
      var more_container = $load_more.parents('section,main').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      // Homepage has a funky load-more div in events that is part of masonry until clicked
      if (breakpoint_medium && $('.home.page').length && $(e.target).parents('.events-buttons').length) {
        var lm = $('.event-cal').addClass('loaded-more').find('.events .events-buttons');
        // Remove load-more from masonry and relayout
        $('.events').masonry('remove', lm);
        $('.events').masonry();
      }

      $.ajax({
          url: wp_ajax_url[0],
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              page: page+1,
              per_page: per_page,
              past_events: past_events,
              focus_area: focus_area,
              division: division,
              exhibitions: exhibitions,
              program: program,
              prox_zip: prox_zip,
              prox_miles: prox_miles
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
        $('.thought-of-the-day').velocity({opacity: 0, left: -50}, { easing: 'easeInSine', duration: 150,
          complete: function(e) {
            $('.thought-of-the-day').css('left',50).addClass('submitting-thought').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
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
          url: wp_ajax_url[0],
          method: 'post',
          data: data,
          success: function(response) {
            $form.removeClass('working');
            if (response.success) {
              $('.thought-of-the-day').velocity({opacity: 0, left: -50}, { easing: 'easeInSine', duration: 150,
                complete: function(e) {
                  $('.new-thought-form')[0].reset();
                  $('.thought-of-the-day .response').text(response.data.message);
                  $('.thought-of-the-day').css('left',50).removeClass('submitting-thought').addClass('thought-submitted').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
                }
              });

            } else {
              alert('There was an error: '+response.data.message);
            }
          }
      });
    });
  }

  function _cancelThoughtSubmit() {
    if ($('.thought-of-the-day').is('.submitting-thought,.thought-submitted')) {
      $('.thought-of-the-day').velocity({opacity: 0, left: 50}, { easing: 'easeInSine', duration: 150,
        complete: function(e) {
          $('.thought-of-the-day').css('left',-50).removeClass('submitting-thought thought-submitted').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
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
    breakpoint_small = (screenWidth > breakpoint_array[0]);
    breakpoint_medium = (screenWidth > breakpoint_array[1]);
    breakpoint_large = (screenWidth > breakpoint_array[2]);

    // Adjust position of Thought of the Day for smaller quotes
    if (!no_header_text && $sidebar.length) {
      if (breakpoint_medium) {
        var sidebar_height = $sidebar.height();
        var adjustment = 270;
        // Shorter TOD giving sidebars guff
        if ($tod.length && $tod.height() <= 406) {
          adjustment = $tod.height() - 136;
        }
        $sidebar.css('margin-top', -adjustment);
      } else {
        $sidebar.css('margin-top', '');
      }
    } else if (page_at === 'homepage' && $tod.length) {
      // Homepage TOD adjustment
      if (breakpoint_medium) {
        var header_height = $('header.page-header').height();
        var tod_height = $tod.height();
        var top = header_height - 270;
        if (tod_height <= 346) {
          top = top + 406 - tod_height;
        }
        $('.thought-of-the-day-wrapper').css('top', top);
      } else {
        $('.thought-of-the-day-wrapper').css('top', '');
      }
    }
  }

  // Called on scroll
  function _scroll(dir) {
    var wintop = $(window).scrollTop();
    $('#map').toggleClass('sticky', (wintop > mapTop));
  }

  // Public functions
  return {
    init: _init,
    resize: _resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    },
    setMapView: function(lat, lng, zoom) {
      map.setView([lat, lng], zoom);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(IHC.init);

// Zig-zag the mothership
jQuery(window).resize(IHC.resize);
