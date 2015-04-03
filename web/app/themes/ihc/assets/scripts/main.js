// IHC - Firebelly 2015

// good design for good reason for good namespace
var IHC = (function($) {

  var screen_width = 0,
      is_desktop = false,
      page_cache = [],
      $content,
      $backnav,
      $body,
      $document,
      $nav,
      map,
      History = window.History,
      rootUrl = History.getRootUrl(),
      loadingTimer;

  function _init() {
      // set screen size vars
      _resize();

      // init state
      State = History.getState();

      $document = $(document);
      $body = $('body');
      $content = $('#main-content');
      $nav = $('.site-nav');

      // fit them vids!
      $content.fitVids();

      // init behavior for various sections
      _initThoughtSubmit();
      _initSearch();
      _initNav();
      _initMap();
      _initAjaxLinks();
      _initMenuToggle();
      _initSliders();
      _initMasonry();
      _initLoadMore();

      // initial nav update based on URL
      _updateNav();

      // Esc handlers
      $(document).keyup(function(e) {
        if (e.keyCode === 27) {
          _hideSearch();
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
    $('.search-toggle, .internal-search-toggle').on('click', function (e) {
      e.preventDefault();
      if (!$('.search-wrap').hasClass('active')) {
        $('.search-wrap').addClass('active'); 
        $('.search-form input').focus(); 
      } else {
        _hideSearch();
      }
    });

    $body.on('click', '.search-container', function (e) {
      if (!$(e.target).is('.search-field')) {
        _hideSearch();
      }
    });
  }

  function _hideSearch() {
    $('.search-wrap').removeClass('active');
  }

  function _initMap() {
    if ($('#map').length) {
      L.mapbox.accessToken = 'pk.eyJ1IjoiZmlyZWJlbGx5ZGVzaWduIiwiYSI6IlZMd0JwWWcifQ.k9GG6CFOLrVk7kW75z6ZZA';
      map = L.mapbox.map('map', 'firebellydesign.lkh3a3i1').setView([41.843, -88.075], 11);
      
      var featureLayer = L.mapbox.featureLayer().addTo(map);
      var geoJSON = [];

      // set custom icons
      featureLayer.on('layeradd', function(e) {
        var marker = e.layer,
          feature = marker.feature;
        marker.setIcon(L.icon(feature.properties.icon));
      });

      // any map-points on page? add to map
      $('.map-point').each(function() {
        var $point = $(this);
        if ($point.data('lng')) {
          geoJSON.push({
              type: 'Feature',
              geometry: {
                  type: 'Point',
                  coordinates: [ $point.data('lng'), $point.data('lat') ]
              },
              properties: {
                  title: $point.data('title'),
                  description: $point.data('desc'),
                  icon: {
                    "iconUrl": "/app/themes/ihc/dist/images/marker.png",
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
      featureLayer.setGeoJSON(geoJSON);
      // set bounds to markers
      map.fitBounds(featureLayer.getBounds());
    }
  }

  // Ajaxify all internal links in content area
  function _initAjaxLinks() {
    $content.find('a:internal:not(.no-ajaxy)').each(function() {
      var href = $(this).attr('href');
      if (!href.match(/\.(jpg|png|gif|pdf)$/)) {
        $(this).click(function(e) {
          e.preventDefault();
          History.pushState({}, '', href);
        });
      }
    });
  }

  function _updateTitle() {
    var title = $content.find('.content:first').data('post-title');
    if (title === '' || title === 'Main') {
      title = 'IHC';
    } else {
      title = title + ' | IHC';
    }
    // this bit also borrowed from Ajaxify
    document.title = title;
    try {
      document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
    } catch (Exception) {}
  }

  // handles main nav
  function _initNav() {

    $(window).bind('statechange',function(){
      var State = History.getState(),
          url = State.url,
          relative_url = url.replace(rootUrl,''),
          parent_li;

      if (State.data.ignore_change) { return; }

      if (!page_cache[encodeURIComponent(url)]) {
        loadingTimer = setTimeout(function() { $content.addClass('loading'); }, 500);
        $.post(
          url,
          function(res) {
            page_cache[encodeURIComponent(url)] = res;
            IHC.updateContent();
          }
        );
      } else {
        _updateContent();
      }
    });
  }

  function _updateContent() {
    var State = History.getState();
    var new_content = page_cache[encodeURIComponent(State.url)];
    // $content.removeClass('fadeInRight').addClass('fadeOutRight');
    setTimeout(function() {
      $content.html(new_content);
      // pull in body class from data attribute
      $body.attr('class', $content.find('.content:first').data('body-class'));
      if (loadingTimer) { clearTimeout(loadingTimer); }
      $content.removeClass('loading');

      _updateTitle();
      _initAjaxLinks();
      _initSliders();
      _initMasonry();
      $content.fitVids();

      // scroll to top
      _scrollBody($body, 250, 0);

      // track page view in Analytics
      _trackPage();

    }, 150);
  }

  function _updateNav() {
  }

  function _initMenuToggle(){
    $('.menu-toggle').on('click', function (e) {
      e.preventDefault();
      _toggleMobileMenu();
    });
  }

  function _toggleMobileMenu() {
    $('.menu-toggle-wrap').toggleClass('menu-open');
    $('#sidebar').toggleClass('active');
  }

  function _initSliders(){
    $('.slider').slick({
      slide: '.slide-item',
      autoplay: $('.home.page').length>0,
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
    var $container = $('.masonry');
    $container.masonry({
      itemSelector: 'article',
      transitionDuration: '.3s'
    });
  }

  function _initLoadMore() {
    $document.on('click', '.happenings .load-more a', function(e) {
      e.preventDefault();
      var load_more = $(this).closest('.load-more');
      var page = parseInt(load_more.attr('data-page-at'));
      var per_page = parseInt(load_more.attr('data-per-page'));
      var masonry_container = load_more.parents('section').find('.masonry');
      loadingTimer = setTimeout(function() { masonry_container.addClass('loading'); }, 500);
      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'get_news_posts',
              page: page + 1,
              per_page: per_page
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            masonry_container.append($data).removeClass('loading');
            masonry_container.masonry('appended', $data, true);
            load_more.attr('data-page-at', page+1);
            _initAjaxLinks();

            // hide load more if last page
            if (load_more.attr('data-total-pages') === page + 1) {
                load_more.addClass('hide');
            }
          }
      });
    });
  }

  function _initThoughtSubmit() {
    $document.on('click', 'a.submit-thought', function(e) {
      e.preventDefault();
      $('.thought-wrapper').addClass('hide');
      $('.submit-thought-wrapper').removeClass('hide');
    });
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
    is_desktop = screenWidth > 768;
  }


  // public functions
  return {
    init: _init,
    resize: _resize,
    updateContent: _updateContent,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    }
  };

})(jQuery);

// fire up the mothership
jQuery(document).ready(IHC.init);
// zig-zag the mothership
jQuery(window).resize(IHC.resize);


(function($){
  // Internal Helper (from Ajaxify)
  $.expr[':'].internal = function(obj, index, meta, stack){
    // Prepare
    var
      $this = $(obj),
      url = $this.attr('href')||'',
      isInternalLink,
      rootUrl = History.getRootUrl();

    // Check link
    isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;
    
    // Ignore or Keep
    return isInternalLink;
  };
})(jQuery);