
jQuery(document).ready(function ($) {
    var slider = $('.slider').bxSlider({
        controls: true,
        pager: false,
        auto: false,
        autoStart: true,
        autoDelay: 2500,
        autoHover: true,
        responsive: true,
        // infiniteLoop: true,
        adaptiveHeight: true,
        useCSS: false
    });

    $('.pager').on('mouseover click', 'a', function (e) {
        e.preventDefault();
        slider.goToSlide($(this).data('slide-index'));
        slider.stopAuto();
      });
  
    $('.pager').on('mouseleave', 'a', function (e) {
        e.preventDefault();
        slider.startAuto();
      });
});