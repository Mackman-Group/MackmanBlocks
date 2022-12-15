<?php
/**
 * Title: Footer with text, links, buttons.
 * Slug: mackman/footer-three-columns
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport Width: 1280
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|small","bottom":"var:preset|spacing|small"},"margin":{"top":"0px"}}},"layout":{"type":"constrained"},"fontSize":"small"} -->
<div class="wp-block-group alignfull has-small-font-size" style="margin-top:0px;padding-top:var(--wp--preset--spacing--small);padding-bottom:var(--wp--preset--spacing--small)">
<!-- wp:group {"align":"wide","layout":{"type":"flex","allowOrientation":false,"justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph -->
<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> All rights reserved.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>A Mackman Group collaboration - Market Research by <a class="site-credits" href="https://www.mackmanresearch.co.uk" target="_blank" rel="nofollow">Mackman Research</a> | Website Design by <a class="site-credits" href="https://mackman.co.uk" target="_blank" rel="nofollow">Mackman</a></p>
<!-- /wp:paragraph -->
<!-- wp:social-links {"iconColor":"base","iconColorValue":"#fff","iconBackgroundColor":"contrast","iconBackgroundColorValue":"#000","className":"is-style-default","style":{"spacing":{"blockGap":"10px"}}} -->
<ul class="wp-block-social-links has-icon-color has-icon-background-color is-style-default">
<!-- wp:social-link {"url":"#","service":"facebook"} /-->
<!-- wp:social-link {"url":"#","service":"instagram"} /-->
<!-- wp:social-link {"url":"#","service":"twitter"} /-->
</ul>
<!-- /wp:social-links -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
