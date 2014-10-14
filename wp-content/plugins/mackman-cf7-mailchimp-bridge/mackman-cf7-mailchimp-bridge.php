<?php
/*
Plugin Name: Contact Form -> MailChimp Bridge
Version: 0.1
Plugin URI: http://www.mackmangroup.co.uk
Description: Auto-subscribes visitors completing standard contact form to MailChimp mailing list
Author: James Royce
Author URI: http://www.mackman.co.uk
*/

function mg_contact_subscribe ( &$WPCF7_ContactForm ) {
    $data = $WPCF7_ContactForm->posted_data;
    //General contact form = 7468
    $form_id = 7468;
    if ( $data['_wpcf7'] == $form_id ) {
        //Check newsletter field - it's a checkbox so any content must be a 'tick'!
        if ( $data['mc-subscribe'] && 0) {
            //Subscribe - yay!
            $mc = new mackman_mailchimp( "095152989be3c2ef5585e1dc65ac902d-us7" );
            $mc->list_id = "120b2f8f96";
            $mc->subscriber_email = $data['your-email'];
            $mc->subscriber_first_name = $data['your-name'];
            $mc->subscriber_last_name = "";
            $mc->subscribe();
            mail("james@mackman.co.uk","cf7 dump", print_r($mc,TRUE));
        }
    }
    //mail("james@mackman.co.uk","cf7 dump", print_r($WPCF7_ContactForm->posted_data,TRUE));
}

add_action("wpcf7_before_send_mail", "mg_contact_subscribe");