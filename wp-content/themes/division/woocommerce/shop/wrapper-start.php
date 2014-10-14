<?php
/*
 * Custom Division shop content wrapper
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$main_container_classes = bk_get_main_container_classes( get_the_ID() );
echo '<div id="bk-fullscreen-background-wrap">';
echo '<div id="bk-content-inner-wrap" class="' . $main_container_classes["bk-content-inner-wrap"] .'">';
echo '<div id="bk-main-wrap" class="row-fluid">';
echo '<div id="bk-content-wrap" class="' . $main_container_classes["bk-content-wrap"]. '">';
?>