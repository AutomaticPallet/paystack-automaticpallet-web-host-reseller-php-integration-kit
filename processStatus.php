<?php 
	require_once("lib/PayStack/Paystack.php");
	$status = $_REQUEST['status'];
	$paystack = new Paystack("pk_test_xxxxxxxxxxxxxxxxxxxx"); // replace with your PayStack private key
	$verify = $paystack->transaction->verify({
		'reference' => $status
	});
	if (!$verify->success)
	{
		exit($verify->message);
	}
	if ('success' == $verify->data->status)
	{
		echo "GoodToRedirect";
	}
?>