// 1 - Build cookies-compliance container and content.
var cookies_compliance = "<div id='cookies_compliance'>";
cookies_compliance += "<div id='cookies_compliance-text-container'>";
cookies_compliance += "<p id='message-1'>Hello. As a transparent and ethical company, we want to let you know that we've changed our Cookies Policy. For more information on what this means, <a href='#' rel='facybox' id='find-out-more'>simply click here</a>.</p>";
cookies_compliance += "</div>";
cookies_compliance += "</div>";

jQuery(document).ready(function() {
  // 2 - Load jsCookie library
  var jsc = new jsCookie();
  // 3 - Check if tracking cookie is already set.
  if( jsc.read('tracking-permission') == 'true' ) {
    // Do nothing.
  } // Close 'if' permission is set
  else {
    // Insert additional margin
    jQuery('body').append( cookies_compliance );
  } // Close  'else' permission is not set
  // 4 - If the button is clicked, set cookie.
  jQuery('#find-out-more').live( 'click', function(e) {
    jsc.create('tracking-permission', 'true', [365,0,0,0]);
    e.preventDefault();
    jQuery.facybox({ ajax: '/wp-content/plugins/cookies-compliance/policy.html' });
    jQuery('#cookies_compliance').hide( 'slow' );
  });
});