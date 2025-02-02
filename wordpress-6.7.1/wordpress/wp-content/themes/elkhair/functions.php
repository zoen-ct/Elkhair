<?php

function elkhair_register_menus() {
    register_nav_menus(array(
        'header-menu' => __('Menu en-tête', 'elkhair-studios'),
    ));
}
add_action('init', 'elkhair_register_menus');
