(function ($) {
    "use strict";

    // ============== Header Hide Click On Body Js Start ========

    $('.header-button').on('click', function () {
      $('.body-overlay').toggleClass('show')
    });
    $('.body-overlay').on('click', function () {
      $('.header-button').trigger('click')
      $(this).removeClass('show');
    });

    // ========================== Header Hide Scroll Bar Js Start =====================
    $('.navbar-toggler.header-button').on('click', function () {
      $('body').toggleClass('scroll-hide-sm')
    });
    $('.body-overlay').on('click', function () {
      $('body').removeClass('scroll-hide-sm')
    });

    // ========================== add active class to current page Js Start =====================
    function dynamicActiveMenuClass(selector) {
      let FileName = window.location.href.split("/").reverse()[0];

      selector.find("li").each(function () {
        let anchor = $(this).find("a");
        if ($(anchor).attr("href") == FileName) {
          $(this).addClass("active");
        }
      });

      // if any li has active element add class
      selector.children("li").each(function () {
        if ($(this).find(".active").length) {
          $(this).addClass("active");
        }
      });
      // if no file name return
      if ("" == FileName) {
        selector.find("li").eq(0).addClass("active");
      }
    }

    if ($('ul').length) {
      dynamicActiveMenuClass($('ul'));
    }

    // ========================== Feature SIdebar Js Start =====================
    $('.filter-button').on('click', function () {
      $('.feature-sidebar').addClass('show');
      $('.sidebar-overlay').addClass('show');
    });
    $('.feature-sidebar__close, .sidebar-overlay').on('click', function () {
      $('.feature-sidebar').removeClass('show');
      $('.sidebar-overlay').removeClass('show');
    });

    // ================== Password Show Hide Js Start ==========
    $(".toggle-password").on('click', function () {
      var input = $(this).siblings('input');
      $(this).toggleClass("fa-eye");
      if (input.attr("type") == "password") {
        $(input).attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
    // =============== Password Show Hide Js End =================

    // ===================== Table Delete Column Js Start =================
    $('.delete-icon').on('click', function () {
      $(this).closest('tr').addClass('d-none')
    });
    // ===================== Table Delete Column Js End =================

    // =========================Gallery magnific Popup Icon Js Start =====================
    $('.gallery-thumb').magnificPopup({
      type: 'image',
      gallery: {
        enabled: true
      }
    });

    // ============================ToolTip Js Start=====================
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    // ============================ToolTip Js End========================

    // ================== Sidebar Menu Js Start ===============
    // Sidebar Dropdown Menu Start
    $(".has-dropdown > a").click(function () {
      $(".sidebar-submenu").slideUp(200);
      if (
        $(this)
          .parent()
          .hasClass("active")
      ) {
        $(".has-dropdown").removeClass("active");
        $(this)
          .parent()
          .removeClass("active");
      } else {
        $(".has-dropdown").removeClass("active");
        $(this)
          .next(".sidebar-submenu")
          .slideDown(200);
        $(this)
          .parent()
          .addClass("active");
      }
    });
    // Sidebar Dropdown Menu End


    // ===================== Sidebar Menu Js End =================

    // ==================== Dashboard User Profile Dropdown Start ==================
    $('.user-info__button').on('click', function () {
      $('.user-info-dropdown').toggleClass('show');
    });
    $('.user-info__button').attr('tabindex', -1).focus();

    $('.user-info__button').on('focusout', function () {
      $('.user-info-dropdown').removeClass('show');
    });
    // ==================== Dashboard User Profile Dropdown End ==================

    // ==========================================
    //      End Document Ready function
    // ==========================================

    // ========================= Preloader Js Start =====================
    // Hide instantly as soon as the DOM is ready: no fade, no window
    // load-event wait, so the light overlay never flashes on navigation.
    var hidePreloader = function () { $('.preloader-wrapper').hide(); }
    hidePreloader();
    // ========================= Preloader Js End=====================

    // ========================= Header Sticky Js Start ==============
    $(window).on('scroll', function () {
      if ($(window).scrollTop() >= 150) {
        $('.header').addClass('fixed-header');
      }
      else {
        $('.header').removeClass('fixed-header');
      }
    });
    // ========================= Header Sticky Js End===================

    //============================ Scroll To Top Icon Js Start =========
    var btn = $('.scroll-top');

    $(window).scroll(function () {
      if ($(window).scrollTop() > 300) {
        btn.addClass('show');
      } else {
        btn.removeClass('show');
      }
    });

    btn.on('click', function (e) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: 0 }, '300');
    });
    //========================= Scroll To Top Icon Js End ======================
    $("label.form-label").addClass("form--label");


  })(jQuery);

