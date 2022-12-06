<?php
/*
    Plugin Name: Middey Project 1
    Plugin URI: https://github.com/towoju5
    Description: Assignment by Middey for Post Type and API
    Author: Emmanuel Towoju
    Version: 1.0
    Author URI: https://github.com/towoju5
*/


function middey_vacancies()
{
    $supports = [
        'title',
        'editor',
        'author',
        'thumbnail',
        'excerpt',
        'custom-fields',
        'comments',
        'revisions',
        'post-formats',
    ];

    $labels = [
        'name' => _x('Middey Jobs', 'plural'),
        'singular_name' => _x('Middey Job', 'singular'),
        'menu_name' => _x('Middey Jobs', 'admin menu'),
        'name_admin_bar' => _x('Middey Jobs', 'admin bar'),
        'add_new' => _x('Add Vacancy', 'add new'),
        'add_new_item' => __('Add New Job'),
        'new_item' => __('New Job'),
        'edit_item' => __('Edit Job'),
        'view_item' => __('View Job'),
        'all_items' => __('All Job'),
        'search_items' => __('Search Middey Jobs'),
        'not_found' => __('No Job found.'),
    ];
    $args = [
        'supports' => $supports,
        'labels' => $labels,
        'public' => true,
        'query_var' => true,
        'rewrite' => [
            'slug' => 'middey-jobs'
        ],
        'has_archive' => true,
        'hierarchical' => false,
    ];

    register_post_type('middey_jobs', $args);
}
add_action('init', 'middey_vacancies');


function register_endpoint($data)
{
    $posts = get_posts([
        'post_type' => 'middey_jobs'
    ]);

    if (empty($posts)) {
        return json_encode([
            'status'  =>  true,
            'data'    =>  [
                'msg' =>  'No Jobs available at the moment',
             ],
        ]);
    }

    return $posts;
}


// index 
add_action('rest_api_init', function () {
    register_rest_route('middey/v1', '/jobs', array(
        'methods' => 'GET',
        'callback' => 'register_endpoint',
    ));
});
