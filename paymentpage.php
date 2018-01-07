<?php
	session_start();
	require("functions.php");	//file which has required functions
?>	 	
		
<html>
<head><title>Payment Page </title>
</head>
<body bgcolor="white">

<?php
		
		
		$key = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"; //replace ur 32 bit secure key , Get your secure key from your Reseller Control panel
		
		//This filter removes data that is potentially harmful for your application. It is used to strip tags and remove or encode unwanted characters.
		$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
		
		//Below are the  parameters which will be passed from foundation as http GET request
		$paymentTypeId = $_GET["paymenttypeid"];  //payment type id
		$transId = $_GET["transid"];			   //This refers to a unique transaction ID which we generate for each transaction
		$userId = $_GET["userid"];               //userid of the user who is trying to make the payment
		$userType = $_GET["usertype"];  		   //This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
		$transactionType = $_GET["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)

		$invoiceIds = $_GET["invoiceids"];		   //comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
		$debitNoteIds = $_GET["debitnoteids"];	   //comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"

		$description = $_GET["description"];
		
		$sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
        $accountingCurrencyAmount = $_GET["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency

		$redirectUrl = $_GET["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him

						
		$checksum = $_GET["checksum"];	 //checksum for validation
		
		//Other variables. 
		
		$name = $_GET['name'];
		$company = $_GET['company'];
		$emailAddr = $_GET['emailAddr'];
		$address1 = $_GET['address1'];
		$address2 = $_GET['address2'];
		$address3 = $_GET['address3'];
		$city = $_GET['city'];
		$state = $_GET['state'];
		$country = $_GET['country'];
		$zip = $_GET['zip'];
		$telNoCc = $_GET['telNoCc'];
		$telNo = $_GET['telNo'];
		$faxNoCc = $_GET['faxNoCc'];
		$faxNo = $_GET['faxNo'];
		$resellerEmail = $_GET['resellerEmail'];
		$resellerURL = $_GET['resellerURL'];
		$resellerCompanyName = $_GET['resellerCompanyName'];

		if(verifyChecksum($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum))
		{
			//YOUR CODE GOES HERE			

		/** 
		* since all these data has to be passed back to foundation after making the payment you need to save these data
		*	
		* You can make a database entry with all the required details which has been passed from foundation.  
		*
		*							OR
		*	
		* keep the data to the session which will be available in postpayment.php as we have done here.
		*
		* It is recommended that you make database entry.
		**/

			

			
			$_SESSION['redirecturl']=$redirectUrl;
			$_SESSION['transid']=$transId;
			$_SESSION['sellingcurrencyamount']=$sellingCurrencyAmount;
			$_SESSION['accountingcurencyamount']=$accountingCurrencyAmount;
			
			//Insert the values into the database. 
			
			/*if ($conn = mysqli_connect("DBHost", "username", "password", "table")) 
				// replace the DB parameters with yours
			{
				if ($query = mysqli_query($conn, "INSERT INTO records (paymenttypeid, transid, userid, usertype, transactiontype, invoiceids, debitnoteids, description, sellingcurrencyamount, accountingcurrencyamount, redirecturl, checksum) VALUES('$paymentTypeId', '$transId', '$userId', '$userType', '$transactionType', '$invoiceIds', '$debitNoteIds', '$description', '$sellingCurrencyAmount', '$accountingCurrencyAmount', '$redirectUrl', '$checksum')")) 
				{
					//Do nothing it worked. 
				}
				else 
				{
					die("Failed to record data".mysqli_error($conn));
				}
			}
			else 
			{
				die("Could not connect to mysql");
			}*/
?>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script type="text/javascript">
	function payWithPaystack(){
		var handler = PaystackPop.setup({
			key: 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
			email: '<?php $emailAddr = $_GET['emailAddr']; echo $emailAddr; ?>',
			amount: '<?php $sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; $sellingCurrencyAmountInNaira = $sellingCurrencyAmount * 100; echo $sellingCurrencyAmountInNaira; ?>',
			ref: '<?php $transId = $_GET["transid"]; echo $transId; ?>', // best to comment out and use Paystacks automatic random ref generated number while testing your integration, else you'll get a duplicate Ref error from Paystack
			metadata: {
				custom_fields: [
				{
					display_name: "Customer Name",
					variable_name: "custom_name",
					value: '<?php $name = $_GET['name']; echo $name; ?>',
				}
				]
			},
			callback: function(response) {
				var data = response.reference; 
				window.location = "postpayment.php?status=" + data;
			},
			onClose: function closeCurrentWindow() {
				window.close();
			}
		});
		handler.openIframe();
	}
	window.onload = function()
	{
		payWithPaystack();
	};
</script>

<?php

		}
		else
		{
			/**This message will be dispayed in any of the following case
			*
			* 1. You are not using a valid 32 bit secure key from your Reseller Control panel
			* 2. The data passed from foundation has been tampered.
			*
			* In both these cases the customer has to be shown error message and shound not
			* be allowed to proceed  and do the payment.
			*
			**/

			echo "Checksum mismatch !";			

		}
?>
</body>
</html>
