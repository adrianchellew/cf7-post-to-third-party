<?php

/**
 * Plugin Name:       Contact Form 7 Post to Third Party
 * Description:       Posts Contact Form 7 field data to a third party
 * Version:           1.0
 * Author:            Adrian Chellew
 */

// -----------------------------------------------------------------#
// Send Contact Form 7 data to a third party
// -----------------------------------------------------------------#

function cf7_post_to_third_party($contact_form) {

	$submission = WPCF7_Submission::get_instance();

	if ($submission) {

		// Get the form ID
		$id = $contact_form->id();
		
		// Get the URL of the page the submitted form was on
		$page_url = $submission->get_meta( 'url' );
		
		// Get the form field data
		$posted_data = $submission->get_posted_data();

		// Get the data from each form field by specifiying the field's name.
		$form_field1 = $posted_data['field1-name'];
		$form_field2 = $posted_data['field2-name'];
		$form_field3 = $posted_data['field3-name'];

		// Specify the URL to which the form data will be posted
		$url = 'http://myremoteservice/';

		// Initiate cURL
		$ch = curl_init($url);

		// Create an array with the form data to be posted
		$jsonData = [
			'id'		=> $id,
			'field1'	=> $form_field1,
			'field2'	=> $form_field2,
			'field3'	=> $form_field3,
			'pageUrl'	=> $page_url
		];

		// Encode the array into JSON
		$jsonDataEncoded = json_encode($jsonData);

		// Tell cURL that we want to send a POST request
		curl_setopt($ch, CURLOPT_POST, 1);

		// Attach the encoded JSON string to the POST fields
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

		// Set the content type to JSON
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		
		// Don't return the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Execute the request
		curl_exec($ch);

		// Close cURL resource
		curl_close($ch);

	}
}

add_action( 'wpcf7_mail_sent', 'cf7_post_to_third_party', 10, 1);

?>