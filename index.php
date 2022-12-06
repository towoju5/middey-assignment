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

function middey_job_search()
{
    register_rest_route('middey/search', 'jobs', [
        'methods'  => WP_REST_SERVER::READABLE,
        'callback' => 'middey_search_results'
    ]);
}
 
function middey_search_results($data)
{
    $search_query = new WP_Query([
        'post_type'      => ['middey_jobs'],
        'posts_per_page' => 10,
        's'              => sanitize_text_field($data['term'])
    ]);
 
    $results = [];
 
    // basic error handling
    if (false === isset($data['term']) ) {
        return [
            'error' => 'No search query defined...'
        ];
    } else {
        if (true === empty($data['term'])) {
            return [
                'error' => 'Please give a term to search...'
            ];
        } else {
          	// check if search query is more than 3 characters and search.
          	// if less than 3 return notification
            if (3 > mb_strlen(trim($data['term']))) {
                return [
                    'error' => 'The search term must contain at least 3 characters...'
                ];
            }
        }
    }
 
    // proceed to database query
    while ($search_query->have_posts()) {
        $search_query->the_post();
 
        array_push($results, [
            'title'     => get_the_title(),
            'permalink' => get_the_permalink(),
        ]);
    }
 
    wp_reset_postdata();
 
    return $results;
}
 
add_action('rest_api_init', 'middey_job_search');

