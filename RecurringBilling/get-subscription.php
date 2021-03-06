<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

  function getSubscription($subscriptionId)
  {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
    //there is acode change in the c=bill
		
    // Creating the API Request with required parameters
    $request = new AnetAPI\ARBGetSubscriptionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSubscriptionId($subscriptionId);
    $request->setIncludeTransactions(true);
	    
    // Controller
    $controller = new AnetController\ARBGetSubscriptionController($request);
		
    // Getting the response
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
		
    if ($response != null) 
    {
        if($response->getMessages()->getResultCode() == "Ok")
        {
        	// Success
        	echo "SUCCESS: GetSubscription:" . "\n";
        	// Displaying the details
        	echo "Subscription Name: " . $response->getSubscription()->getName(). "\n";
        	echo "Subscription amount: " . $response->getSubscription()->getAmount(). "\n";
        	echo "Subscription status: " . $response->getSubscription()->getStatus(). "\n";
        	echo "Subscription Description: " . $response->getSubscription()->getProfile()->getDescription(). "\n";
        	echo "Customer Profile ID: " .  $response->getSubscription()->getProfile()->getCustomerProfileId() . "\n";
        	echo "Customer payment Profile ID: ". $response->getSubscription()->getProfile()->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
                $transactions = $response->getSubscription()->getArbTransactions();
                if($transactions != null){
			foreach ($transactions as $transaction) {
                    		echo "Transaction ID : ".$transaction->getTransId()." -- ".$transaction->getResponse()." -- Pay Number : ".$transaction->getPayNum()."\n";
                	}
		}
        }
        else
        {
        	// Error
        	echo "ERROR :  Invalid response\n";	
        	$errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
	  }
    else
    {
        // Failed to get response
        echo "Null Response Error";
    }

    return $response;
	}

	if(!defined('DONT_RUN_SAMPLES'))
		getSubscription("2942461");
 ?>
