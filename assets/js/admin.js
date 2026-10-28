/**
 * Astro Animations - Admin Scripts
 *
 * @package AstroAnimations
 */
(function ($) {
  'use strict';

  $(document).ready(function () {

    // Helper: Toast Notification
    function showToast(msg) {
      var $toast = $('.astro-toast');
      if (!$toast.length) {
        $toast = $('<div class="astro-toast"></div>').appendTo('body');
      }
      $toast.text(msg).addClass('show');
      setTimeout(function () {
        $toast.removeClass('show');
      }, 2400);
    }

    // Copy Shortcode to Clipboard
    $(document).on('click', '.astro-copy-btn', function (e) {
      e.preventDefault();
      var code = $(this).attr('data-code');
      if (!code) return;

      var successMsg = '✓ ' + (window.astroAnimAdmin ? window.astroAnimAdmin.copiedText : 'Shortcode copied to clipboard!');

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(function () {
          showToast(successMsg);
        });
      } else {
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(code).select();
        document.execCommand('copy');
        $temp.remove();
        showToast(successMsg);
      }
    });

    // Auto-generate slug from title
    var $title = $('#anim_title');
    var $slug  = $('#anim_slug');
    var isNew  = !$slug.val();

    if (isNew && $title.length && $slug.length) {
      $title.on('input', function () {
        var val = $(this).val();
        var slugVal = val.toLowerCase()
          .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's')
          .replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
          .replace(/[^a-z0-9\s-]/g, '')
          .trim()
          .replace(/[\s-]+/g, '-');
        $slug.val(slugVal);
      });
    }

    // Tabs switching
    $('.astro-tab-btn').on('click', function (e) {
      e.preventDefault();
      var tabId = $(this).attr('data-tab');
      $('.astro-tab-btn').removeClass('active');
      $('.astro-tab-content').removeClass('active');
      $(this).addClass('active');
      $('#' + tabId).addClass('active');

      if (tabId === 'preview-tab') {
        renderPreview();
      }
    });

    // Refresh live preview
    $('#astroRefreshPreview').on('click', function (e) {
      e.preventDefault();
      renderPreview();
    });

    // Render Preview in Iframe
    function renderPreview() {
      var iframe = document.getElementById('astroPreviewFrame');
      if (!iframe) return;

      var code   = $('#anim_code').val() || '<div style="padding:40px; text-align:center; color:#888;">No code provided yet.</div>';
      var width  = $('#container_width').val() || '100%';
      var height = $('#container_height').val() || 'auto';
      var align  = $('#anim_align').val() || 'center';
      var bg     = $('#anim_bg').val() || 'transparent';

      var margin = '0 auto';
      if (align === 'left') margin = '0 auto 0 0';
      if (align === 'right') margin = '0 0 0 auto';

      var doc = iframe.contentDocument || iframe.contentWindow.document;
      doc.open();
      doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Preview</title>');
      doc.write('<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">');
      doc.write('<style>');
      doc.write('body{margin:0; padding:24px 16px; background:#f6f8ff; font-family:"Outfit",sans-serif; color:#1E2340; min-height:100vh; box-sizing:border-box;}');
      doc.write('.preview-wrap{width:100%; max-width:' + width + '; min-height:' + height + '; margin:' + margin + '; background:' + bg + '; isolation:isolate; contain:layout;}');
      doc.write('</style></head><body>');
      doc.write('<div class="preview-wrap">' + code + '</div>');
      doc.write('</body></html>');
      doc.close();
    }

    // Allow tab indentation inside code editor
    $('#anim_code').on('keydown', function (e) {
      if (e.key === 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end   = this.selectionEnd;
        var val   = $(this).val();
        $(this).val(val.substring(0, start) + '  ' + val.substring(end));
        this.selectionStart = this.selectionEnd = start + 2;
      }
    });

  });
})(jQuery);
