<?php

define("PHP_ENV",false);
ini_set("display_errors",PHP_ENV?"ON":"Off");

//require_once "../vendor/autoload.php";
//require_once "authentication/authentication.php";
//require_once "withdraw/Withdrawal.php";
//require_once "deposit/DepositService.php";
//require_once "transfer/transfer.php";
//require_once "serviceauthentication/serviceauthentication.php";
require_once "billpayment/billpayment.php";
require_once "billpayment/ChargeStub.php";
require_once "billpayment/ServiceAuthenticationStub.php";

use Operation\Authentication;
use Operation\DepositService;
use Operation\Withdrawal;
use Operation\Transfer;
use Operation\BillPayment;
use Operation\ChargeStub;
use Operation\ServiceAuthenticationStub;

$logFile = "../errorlog.txt";
$service = $_POST["service"];
$session = isset($_COOKIE["authentication"])?$_COOKIE["authentication"]:null;


function output2JSON($outputIns){
    $response = array();
    if (isset($outputIns->errorMessage)){
      $response["isError"] = true;
      $response["message"] = $outputIns->errorMessage;
    }
    else{
      $response["isError"] = false;
      $response["data"] = array_filter((array)$outputIns,"strlen");
    }
    return $response;
}
try{
	
   if ($service == "BillPayment"){
        $transaction = $_POST["transaction"];
        $billPayment = new BillPayment($transaction["accountNumber"],$transaction["chargeType"]);
        echo json_encode($billPayment->pay($transaction["bill_type"]));
   }
   elseif ($service == "BillPaymentInq"){
        $transaction = $_POST["transaction"];
		$chargeAmount = new ChargeStub($transaction["accountNumber"],$transaction["chargeType"]);
		echo json_encode($chargeAmount->getBill()); 
   }
   elseif ($service == "ServiceAuthenticationStub"){
        $transaction = $_POST["transaction"];
		$answer = new ServiceAuthenticationStub($transaction["accountNumber"]);
		echo json_encode($answer->acauProvider()); 
   }
	
  /* if ($service == "Authentication"){
    $transaction = $_POST["transaction"];
    $auth = new Authentication($transaction["acct_num"],$transaction["pin"]);
    echo json_encode($auth->login());
  }
  elseif($session)
  {
      if ($service == "Deposit"){
        $transaction = $_POST["transaction"];
        $deposit = new DepositService($session);
        echo json_encode(output2JSON($deposit->deposit($transaction["amount"])));
      }
      elseif ($service == "Withdraw"){
        $transaction = $_POST["transaction"];
        $withdrawal = new Withdrawal($session);
        echo json_encode(output2JSON($withdrawal->withdraw($transaction["amount"])));
      }
      elseif ($service == "Transfer"){
        $transaction = $_POST["transaction"];
        $transfer = new Transfer($transaction["srcNumber"],Withdrawal::class,DepositService::class);
        echo json_encode($transfer->doTransfer($transaction["targetNumber"],$transaction["amount"]));
      }
      elseif ($service == "BillPayment"){
        $transaction = $_POST["transaction"];
        $billPayment = new BillPayment($session);
        echo json_encode($billPayment->pay($transaction["bill_type"]));
      }
      elseif ($service == "BillPaymentInq"){
        $transaction = $_POST["transaction"];
		$billPaymentInq = new BillPayment();
		echo json_encode($billPaymentInq->getBill($transaction["accountNumber"],$transaction["chargeType"])); 
      }
      elseif ($service == "ServiceAuthentication"){
        $result["isError"] = true;
        try{
          $result = ServiceAuthentication::accountAuthenticationProvider($session);
          $result["isError"] = false;
        }
        catch(AccountInformationException $e){
          $result["message"] = $e->getMessage();
        }
        echo json_encode($result);
      }
      else{
        http_response_code(501);
        return;
      }
  }
  else{
    http_response_code(401);
    return;
  } */

}catch(Error $e){
  date_default_timezone_set('Asia/Bangkok');
  $file = fopen($logFile,"a+");
  fwrite($file,"Log Time: ".date("d-m-Y H:i:sa") . "\n");
  fwrite($file,$e."\n\n");
  http_response_code(400);
  return;
}
?>
