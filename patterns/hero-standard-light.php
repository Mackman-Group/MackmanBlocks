<?php
/**
 * Title: Standard Light
 * Slug: mackman/hero-standard-light
 * Categories: hero
 * Viewport Width: 1280
 * Description: The page title, a subtitle bound to post meta, and the featured image. Content-only locked; used as the first section of the locked Page block template.
 */
?>
<!-- wp:group {"metadata":{"name":"Hero"},"templateLock":"contentOnly","style":{"spacing":{"padding":{"top":"var:preset|spacing|large","bottom":"var:preset|spacing|large"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--large);padding-bottom:var(--wp--preset--spacing--large)">
	<!-- wp:post-title {"level":1} /-->

	<!-- wp:paragraph {"metadata":{"name":"Subtitle","bindings":{"content":{"source":"core/post-meta","args":{"key":"page_subtitle"}}}},"placeholder":"Add a subtitle…","fontSize":"medium"} -->
	<p class="has-medium-font-size">Add a subtitle…</p>
	<!-- /wp:paragraph -->

	<!-- wp:post-featured-image {"style":{"spacing":{"margin":{"top":"var:preset|spacing|medium"}}}} /-->
</div>
<!-- /wp:group -->
