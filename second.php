<?php

/**
 * Plugin Name:       My Second Assignment
 * Plugin URI:        https://middey.com/
 * Description:       Assignment by Horpeyemi
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Emmanuel Towoju    
 * Author URI:        https://towoju.com.ng/
 */

function middeyRegisterUser()
{
	$data = [
		'first_name'	=>	sanitize_text_field($_POST('first_name')),
		'last_name'		=>	sanitize_text_field($_POST('last_name')),
		'username'		=>	sanitize_text_field($_POST('username')),
		'user_email'	=>	sanitize_email($_POST('user_email')),
		'user_password'	=>	sanitize_text_field($_POST('user_password')),
	];

	if($user = wp_insert_user($data)){
		$data = [
			'user_id'	=>	$user->ID
		];
		return get_success_response(200, "User created successfully", $data);
	}	
}

function middeyLoginUser()
{
	$credentials = $_REQUEST;
	$user = wp_authenticate( sanitize_text_field($credentials['user_login']), $credentials['user_password'] );

	if ( is_wp_error( $user ) ) {
		return get_error_response(400, "Unable to authenticate user", $user);
	}

	if($user){
		$data = [
			'user_id'	    =>	$user->ID,
            'user_email'    =>  $user->user_email,
            'username'      =>  $user->username,
            'time_of_login' =>  $user->user_last_login,
            'access_token'  =>  $user->access_token
		];
		return get_success_response(200, "Login successful", $data);
	}	
}

function middeyDebitUser(){
    $data['wallet']         = sanitize_text_field($_POST['wallet_id']);
    $data['amount']         = sanitize_text_field($_POST['amount']);
    $data['transactionId']  = sanitize_text_field($_POST['request_id']);
    $data['user_id']        = sanitize_text_field($_POST['user_id']);
    $data['note']           = sanitize_text_field($_POST['note']) ?? NULL;

    $transaction_id = $data['transactionId'];

    $update_balance = update_wallet_balance('debit', $transaction_id, $data);
    // check if transaction ID exits
    if ($post = get_post_meta($transaction_id, 'debit', true)) {
		return [
            'txn_id'    =>  $transaction_id,
            'exist'     =>  'Transaction already exists'
        ];
	}
    
    if($update_balance){
        $arr = [
            'transaction_id'    =>  $update_balance['transaction_id'],
            'user_id'           =>  $update_balance['user_id']
        ];
        return get_success_response(200, "Transaction Completed", $arr);
    }

    return get_error_response(400, "invalid input", ["msg" => $update_balance]);
}

function middeyCreditUser(){
    $data['wallet']         = sanitize_text_field($_POST['wallet_id']);
    $data['amount']         = sanitize_text_field($_POST['amount']);
    $data['transactionId']  = sanitize_text_field($_POST['request_id']);
    $data['user_id']        = sanitize_text_field($_POST['user_id']);
    $data['note']           = sanitize_text_field($_POST['note']) ?? NULL;

    $transaction_id = $data['transactionId'];

    $update_balance = update_wallet_balance('credit', $transaction_id, $data);
    // check if transaction ID exits
    if ($post = get_post_meta($transaction_id, 'credit', true)) {
		return [
            'txn_id'    =>  $transaction_id,
            'exist'     =>  'Transaction already exists'
        ];
	}
    
    if($update_balance){
        $arr = [
            'transaction_id'    =>  $update_balance['transaction_id'],
            'user_id'           =>  $update_balance['user_id']
        ];
        return get_success_response(200, "Transaction Completed", $arr);
    }

    return get_error_response(400, "invalid input", ["msg" => $update_balance]);
}


function update_wallet_balance(string $action='debit', string $transaction_id, array $data)
{
    // Check if Transaction exits
    if ($post = get_post_meta($transaction_id, $action, true)) {
		return [
            'txn_id'    =>  $transaction_id,
            'exist'     =>  'Transaction already exists'
        ];
	}

    // Transaction doesn't exits, so create transaction
}

function get_success_response($code, $msg, $data)
{
	$data = [
		'status_code'	=>	$code,
		'status'		=>	true,
		'message'		=>	"User created successfully",
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
