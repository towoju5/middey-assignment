<?php

/**
 * Plugin Name:       WebHook
 * Plugin URI:        https://middey.com/
 * Description:       Assignment by Horpeyemi
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Emmanuel Towoju    
 * Author URI:        https://towoju.com.ng/
 */

// webhook(get_success_response(200, 'success', ["msg" => "Warning Undefined array key rimplenettransaction_type"]));

add_action('rimplenet_create_credit_alert_hook', 'webhook', 0, 1);

function webhook($prop)
{
    $url = "https://webhook.site/14ccaa07-eebd-4637-91c9-5cb17329a48e";
    try {
        $response = wp_remote_post( $url, [
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 0,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => [],
                'body' => json_encode(["msg" => $prop]),
                'cookies' => []
            ]  
        );
        
        if ( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
            return get_error_response(
                400,
                $error_message, 
                [
                    'msg'   =>  $error_message
                ]
            );
        } else {
           return get_success_response(200, 'Webhook sent successfully', $response);
        }
    } catch (\Throwable $th) {
        return get_error_response(
            $th->getCode(),
            $th->getMessage(), 
            [
                'msg'   =>  $th->getMessage()
            ]
        );
    }
}

function get_success_response($code, $msg, $data)
{
	$data = [
		'status_code'	=>	$code,
		'status'		=>	true,
		'message'		=>	$msg ?? "User created successfully",
		'data'			=>	$data
	];
	return $data;
}

function get_error_response($code, $msg, $data)
{
	$data = [
		'status_code'	=>	$code,
		'status'		=>	false,
		'message'		=>	$msg,
		'error'			=>	$data
	];
	return $data;
}
