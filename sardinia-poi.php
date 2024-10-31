<?php
   /*
   Plugin Name: Sardinia POI
   Description: Display points of interest from a dataset of Sardinian territory using a customizable shortcode.
   Version: 1.6
   Author: Matteo Enna
   Author URI: https://matteoenna.it/it/wordpress-work/
   Text Domain: sardinia-poi
   License: GPL2
   */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    require_once (dirname(__FILE__).'/class/sardinia-poi-required.php');

    $scb = new sardiniaPoi_mainClass();
