/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
( function( jQuery ) {
	/*------------------------------------------------------------
	  Body background color, image, position, repeat
	-------------------------------------------------------------*/
	
	jQuery('head').append(
	  '<style id="bk_header_background_color" type="text/css"></style>' +	
	  '<style id="bk_header_font_color" type="text/css"></style>' +
	  '<style id="bk_header_link_color" type="text/css"></style>' + 
	  '<style id="bk_header_link_hover_color" type="text/css"></style>' +
	  '<style id="bk_navigation_text_color" type="text/css"></style>' +
      '<style id="bk_navigation_text_hover_color" type="text/css"></style>' +
      '<style id="bk_navigation_submenu_color" type="text/css"></style>' +
	  '<style id="bk_body_font_color" type="text/css"></style>' +
      '<style id="bk_body_link_color" type="text/css"></style>' +
      '<style id="bk_body_link_hover_color" type="text/css"></style>' +
      '<style id="bk_button_hover_background" type="text/css"></style>' + 
      '<style id="bk_body_accent_color" type="text/css"></style>' +
      '<style id="bk_separator_color" type="text/css"></style>' +
      '<style id="bk_button_background" type="text/css"></style>' +
      '<style id="bk_button_hover_background" type="text/css"></style>' +
      '<style id="bk_image_overlay_background" type="text/css"></style>' +
      '<style id="bk_image_overlay_text_color" type="text/css"></style>' +
      '<style id="bk_image_overlay_tag_color" type="text/css"></style>' +
      '<style id="bk_image_overlay_tag_hover_color" type="text/css"></style>' +
      '<style id="bk_odd_background" type="text/css"></style>' +
      '<style id="bk_body_input_text_area_border" type="text/css"></style>' +
      '<style id="bk_body_input_text_area_background" type="text/css"></style>' +
      '<style id="bk_body_input_text_area_border_focus" type="text/css"></style>' +
      '<style id="bk_body_input_text_area_background_focus" type="text/css"></style>' + 
      '<style id="bk_navigation_portfolio_filter_text_color" type="text/css"></style>' +
      '<style id="bk_navigation_portfolio_filter_text_hover_color" type="text/css"></style>' +
      '<style id="bk_portfolio_filter_text_color" type="text/css"></style>' +
      '<style id="bk_portfolio_filter_text_hover_color" type="text/css"></style>' +
      '<style id="bk_portfolio_thumb_description_background_color" type="text/css"></style>' +
      '<style id="bk_blog_thumb_description_background_color" type="text/css"></style>' +
      '<style id="bk_post_title_color" type="text/css"></style>' +
      '<style id="bk_post_title_hover_color" type="text/css"></style>' +
      '<style id="bk_quote_post_format_background" type="text/css"></style>' +
      '<style id="bk_quote_post_format_text" type="text/css"></style>' +
      '<style id="bk_link_post_format_background" type="text/css"></style>' +
      '<style id="bk_link_post_format_text" type="text/css"></style>' +
      '<style id="bk_link_post_format_text_hover" type="text/css"></style>' +
      '<style id="bk_tweet_post_format_background" type="text/css"></style>' +
      '<style id="bk_tweet_post_format_text" type="text/css"></style>' +
      '<style id="bk_tweet_post_format_link" type="text/css"></style>' +
      '<style id="bk_tweet_post_format_link_hover" type="text/css"></style>' +
      '<style id="bk_chat_post_format_background" type="text/css"></style>' +
      '<style id="bk_chat_post_format_text" type="text/css"></style>' +
      '<style id="bk_button_background" type="text/css"></style>' +
      '<style id="bk_button_hover_background" type="text/css"></style>' +
      '<style id="bk_highlight_background" type="text/css"></style>' +
      '<style id="bk_highlight_text_color" type="text/css"></style>' +
      '<style id="bk_progressbar_background" type="text/css"></style>' +
      '<style id="bk_progressbar_color" type="text/css"></style>' +
      '<style id="bk_price_table_background" type="text/css"></style>' +
      '<style id="bk_price_table_border" type="text/css"></style>' +
      '<style id="bk_price_table_alternate_color" type="text/css"></style>' +
      '<style id="bk_toggle_header_color" type="text/css"></style>' +
      '<style id="bk_toggle_header_hover_color" type="text/css"></style>'
      );
	
	/*
	 *  Header
	 */ 
	
	wp.customize( 'bk_header_font_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_header_font_color').text('#bk-header {color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_header_link_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_header_link_color').text('#bk-header a{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_header_link_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_header_link_hover_color').text('#bk-header a:hover{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_header_background_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('#bk-header').css('background-color', newval );
			jQuery('style#bk_header_background_color').text('#bk-header.bk-collapsible-header #bk-navigation-toggle:after{border-left-color:' + newval + ';} #bk-header.bk-classic-header-top.bk-collapsible-header #bk-navigation-toggle:after, #bk-header.bk-fixed-header-top.bk-collapsible-header #bk-navigation-toggle:after{border-color: transparent; border-top-color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_header_background_image', function( value ) {
		value.bind( function( newval ) {
			jQuery('#bk-header').css('background-image', 'url(' + newval + ')' );
		});
	});
	
	wp.customize( 'bk_header_background_image_position', function( value ) {
		value.bind( function( newval ) {
			jQuery('#bk-header').css('background-position', newval );
		});
	});
	
	wp.customize( 'bk_header_background_image_repeat', function( value ) {
		value.bind( function( newval ) {
			jQuery('#bk-header').css('background-repeat', newval );
		});
	});
	
	wp.customize( 'bk_navigation_text_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_navigation_text_color').text('#bk-header .bk-stacked-menu li a, #bk-header ul.sf-menu > li.menu-item > a, .sf-menu li.menu-item > a, #bk-header .sf-menu li.menu-item > a:visited{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_navigation_text_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_navigation_text_hover_color').text('#bk-header .bk-stacked-menu li > a:hover, #bk-header .bk-stacked-menu li.current-menu-item > a, #bk-header ul.sf-menu > li.menu-item > a:hover, #bk-header ul.sf-menu li.menu-item.current-menu-item > a, #bk-header .sf-menu li.menu-item:hover > a, #bk-header .sf-menu li.menu-item.sfHover > a {color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_navigation_submenu_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_navigation_submenu_color').text('ul.sf-menu ul.sub-menu {background-color:' + newval + ';}');
		});
	});
	
	/*
	 * Body 
	 */
	wp.customize( 'bk_body_background_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap').css('background-color', newval );
		});
	});
	
	wp.customize( 'bk_body_background_image', function( value ) {
		value.bind( function( newval ) {
			jQuery('.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap').css('background-image', 'url(' + newval + ')' );
		});
	});
	
	wp.customize( 'bk_body_background_image_position', function( value ) {
		value.bind( function( newval ) {
			jQuery('.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap').css('background-position', newval );
		});
	});
	
	wp.customize( 'bk_body_background_image_repeat', function( value ) {
		value.bind( function( newval ) {
			jQuery('.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap').css('background-repeat', newval );
		});
	});
	
	/*------------------------------------------------------------
	Body font, link, link:hover color
	------------------------------------------------------------*/
	
	wp.customize( 'bk_body_font_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_font_color').text('body{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_body_link_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_link_color').text('body a{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_body_link_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_link_hover_color').text('body a:hover{color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_separator_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_separator_color').text('h3.widget-title span, .content-separator span.title {border-bottom-color:' + newval + ';}');
		});
	});
	
	wp.customize( 'bk_image_overlay_background', function( value ) {
		value.bind( function( newval ) {
			var opacity = '0.75';
			
			var text = 'div.bk-img-overlay {background-color:' + newval + ' ;}';
			text += 'div.bk-img-overlay {background-color:' + bk_hex2rgba(newval, opacity) + ' ;}';
			
			jQuery('style#bk_image_overlay_background').text(text);
		});
	});
	
	wp.customize( 'bk_image_overlay_text_color', function( value ) {
		value.bind( function( newval ) {
			var text = 'div.bk-img-overlay {color:' + newval + ' ;}';
			text += 'div.title_and_tags div.portfolio-thumb-title-meta-wrap h4.portfolio-thumb-title, div.title_and_cats div.portfolio-thumb-title-meta-wrap h4.portfolio-thumb-title {border-bottom-color:' + newval + ' ;}';

			jQuery('style#bk_image_overlay_text_color').text(text);
		});
	});
	
	wp.customize( 'bk_image_overlay_tag_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_image_overlay_tag_color').text('div.portfolio-thumb-title-meta-wrap span.porfolio-thumb-tags a {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_image_overlay_tag_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_image_overlay_tag_hover_color').text('div.portfolio-thumb-title-meta-wrap span.porfolio-thumb-tags a:hover {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_odd_background', function( value ) {
		value.bind( function( newval ) {
			var style = 'ol.bk_comment_list li.comment div.content-wrap, div.bk-testimonial-body, ol.bk_comment_list div#respond, ul.bk-recent-comments-widget div.bk-recent-comment {background-color:' + newval + ' ;}' + "\n";
			style += 'ol.bk_comment_list li.comment div.content-wrap span.comment-tip, ul.bk-recent-comments-widget div.bk-recent-comment:after {border-right-color:' + newval + ' ; }' + "\n";
			style += 'div.testimonial-tip {border-top-color:' + newval + ';}' + "\n";
			
			jQuery('style#bk_odd_background').text(style);
		});
	});
	
	wp.customize( 'bk_body_input_text_area_border', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_input_text_area_border').text('input[type="text"], input[type="password"], textarea {border-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_body_input_text_area_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_input_text_area_background').text('input[type="text"], input[type="password"], textarea {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_body_input_text_area_border_focus', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_input_text_area_border_focus').text('input[type="text"]:focus, input[type="password"]:focus, textarea:focus {border-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_body_input_text_area_background_focus', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_body_input_text_area_background_focus').text('input[type="text"]:focus, input[type="password"]:focus, textarea:focus {background-color:' + newval + ' ;}');
		});
	});
	
	/*
	 * Portfolio
	 */
	
	wp.customize( 'bk_navigation_portfolio_filter_text_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_navigation_portfolio_filter_text_color').text('#bk-header ul.portfolio-filter li a {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_navigation_portfolio_filter_text_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_navigation_portfolio_filter_text_hover_color').text('#bk-header ul.portfolio-filter li a:hover, #bk-header ul.portfolio-filter li.current a {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_portfolio_filter_text_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_portfolio_filter_text_color').text('ul.portfolio-filter li a {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_portfolio_filter_text_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_portfolio_filter_text_hover_color').text('ul.portfolio-filter li a:hover, ul.portfolio-filter li.current a {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_portfolio_thumb_description_background_color', function( value ) {
		value.bind( function( newval ) {
			var text = 'li.portfolio-item div.portfolio-thumb-description-wrap {background-color:' + newval + ' ;}';
			text += 'li.portfolio-item div.portfolio-thumb-description-wrap:after {border-bottom-color:' + newval + ' ;}';
			jQuery('style#bk_portfolio_thumb_description_background_color').text(text);
		});
	});

	
	/*
	 Blog main rules
	*/
	wp.customize( 'bk_blog_thumb_description_background_color', function( value ) {
		value.bind( function( newval ) {
			var text = 'article.bk-post-summary {background-color:' + newval + ' ;}';
			text += 'article.bk-post-summary.format-image div.bk-blog-content-wrap:after, article.bk-post-summary.format-standard div.bk-blog-content-wrap:after, article.bk-post-summary.format-quote div.bk-blog-content-wrap:after, article.bk-post-summary.format-link div.bk-blog-content-wrap:after, article.bk-post-summary.format-chat div.bk-blog-content-wrap:after, article.bk-post-summary.format-status div.bk-blog-content-wrap:after {border-bottom-color:' + newval + ' ;}';
			
			jQuery('style#bk_blog_thumb_description_background_color').text(text);
		});
	});
	
	wp.customize( 'bk_post_title_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_post_title_color').text('h3.post-entry-title a, span.widget-post-title a {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_post_title_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_post_title_hover_color').text('h3.post-entry-title a:hover, span.widget-post-title a:hover {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_quote_post_format_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_quote_post_format_background').text('div.bk-quote-post-format-featured-inner-wrap {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_quote_post_format_text', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_quote_post_format_text').text('div.bk-quote-post-format-featured-inner-wrap {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_link_post_format_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_link_post_format_background').text('div.bk-link-post-format-featured-inner-wrap {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_link_post_format_text', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_link_post_format_text').text('div.bk-link-post-format-featured-inner-wrap a{color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_link_post_format_text_hover', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_link_post_format_text_hover').text('div.bk-link-post-format-featured-inner-wrap a:hover{color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_background').text('div.bk-status-post-format-featured-inner-wrap {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_text', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_text').text('div.bk-status-post-format-featured-inner-wrap {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_link', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_link').text('div.bk-status-post-format-featured-inner-wrap a{color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_link_hover', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_link_hover').text('div.bk-status-post-format-featured-inner-wrap a:hover{color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_link', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_link').text('div.bk-status-post-format-featured-inner-wrap a{color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_tweet_post_format_link_hover', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_tweet_post_format_link_hover').text('div.bk-status-post-format-featured-inner-wrap a:hover{color:' + newval + ' ;}');
		});
	});
	
	
	wp.customize( 'bk_chat_post_format_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_chat_post_format_background').text('div.bk-chat-post-format-featured-inner-wrap {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_chat_post_format_text', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_chat_post_format_text').text('div.bk-chat-post-format-featured-inner-wrap {color:' + newval + ' ;}');
		});
	});
	
	/*
	 * Shortcodes
	 */
	
	wp.customize( 'bk_button_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_button_background').text('a.bk_button, div#respond p.form-submit input[type=submit], div.bk-protected-form-wrap input[type=submit], a.comment-reply-link, span.bk-cancel-reply-link, a.comment-edit-link, li.widget .tagcloud a {color:' + newval + ' ; border-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_button_hover_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_button_hover_background').text('a.bk_button:hover, div#respond p.form-submit input[type=submit]:hover, div.bk-protected-form-wrap input[type=submit]:hover, a.comment-reply-link:hover, span.bk-cancel-reply-link:hover, a.comment-edit-link:hover, li.widget .tagcloud a:hover {color:' + newval + ' ; border-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_highlight_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_highlight_background').text('em.bk_highlight {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_highlight_text_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_highlight_text_color').text('em.bk_highlight {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_progressbar_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_progressbar_background').text('div.bk-progress-bar-base {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_progressbar_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_progressbar_color').text('div.bk-progress-bar {background-color:' + newval + ' ;}');
		});
	});
	
	
	/*
	  Price Table
	*/
	
	wp.customize( 'bk_price_table_background', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_price_table_background').text('div.bk-pricing-table-column-wrap {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_price_table_border', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_price_table_border').text('div.bk-pricing-table-column-wrap {border-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_price_table_alternate_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_price_table_alternate_color').text('div.bk-pricing-table-feature.bk_alternate {background-color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_toggle_header_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_toggle_header_color').text('div.bk-tabs ul.ui-tabs-nav li a, div.bk-toggle-header, div.bk-toggle.bk-toggle-closed div.bk-toggle-header {color:' + newval + ' ;}');
		});
	});
	
	wp.customize( 'bk_toggle_header_hover_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('style#bk_toggle_header_hover_color').text('div.bk-tabs ul.ui-tabs-nav li.ui-tabs-selected a, div.bk-tabs ul.ui-tabs-nav li.ui-tabs-active a, div.bk-tabs ul.ui-tabs-nav li:hover a, div.bk-toggle-header:hover, div.bk-toggle-header.ui-state-active, div.bk-toggle div.bk-toggle-header {color:' + newval + ' ;}');
		});
	});
	
	
	/*------------------------------------------------------------
	  Page background color, image, position, repeat
	-------------------------------------------------------------*/
	wp.customize( 'bk_page_background_color', function( value ) {
		value.bind( function( newval ) {
			jQuery('body').css('background-color', newval );
		});
	});
	
	wp.customize( 'bk_page_background_image', function( value ) {
		value.bind( function( newval ) {
			jQuery('body').css('background-image', 'url(' + newval + ')' );
		});
	});
	
	wp.customize( 'bk_page_background_image_position', function( value ) {
		value.bind( function( newval ) {
			jQuery('body').css('background-position', newval );
		});
	});
	
	wp.customize( 'bk_page_background_image_repeat', function( value ) {
		value.bind( function( newval ) {
			jQuery('body').css('background-repeat', newval );
		});
	});
	
	wp.customize( 'bk_page_background_image_attachment', function( value ) {
		value.bind( function( newval ) {
			jQuery('body').css('background-attachment', newval );
		});
	});
	
	/*
	 * convert hex with opacity to rgba
	 * 
	 */
	function bk_hex2rgba($hex, $opacity) {
		$hex = $hex.replace("#", "");

		if($hex.length == 3) {
			$r = hexdec($hex.substr(0,1) + '' + $hex.substr(0,1));
			$g = hexdec($hex.substr(1,1) + '' + $hex.substr(1,1));
			$b = hexdec($hex.substr(2,1) + '' + $hex.substr(2,1));
		} else {
			$r = hexdec($hex.substr(0,2));
			$g = hexdec($hex.substr(2,2));
			$b = hexdec($hex.substr(4,2));
		}
		var $rgb = new Array($r, $g, $b, $opacity);
		return 'rgba(' + $rgb.join() + ')'; // returns the rgba values separated by commas
	}
	
	function hexdec (hex_string) {
		  //console.log(hex_string);
		  hex_string = (hex_string + '').replace(/[^a-f0-9]/gi, '');
		  var value = parseInt(hex_string, 16);
		  //console.log(value);
		  return value;
	}
	
} )( jQuery );