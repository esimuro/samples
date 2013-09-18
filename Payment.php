<?php
/**
 * Fines and Payment action for MyResearch module
 *
 * PHP version 5
 *
 */
require_once 'services/MyResearch/MyResearch.php';


class Payment extends MyResearch
{
    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $configArray;
        global $finesIndexEngine;
        $creditCards = array ("Null"=>'Select card type', "A0"=>'American Express', 
                              "I0"=>'Diners Club',  "D0"=>'Discover', 
                              "M0"=>'MasterCard', "V0"=>'Visa' );
        $interface->assign('creditCards',  $this->_creditCardsAccepted($creditCards));
        $interface->assign('years',  $this->_experationYears());
        // Get My Fines
        if ($patron = UserAccount::catalogLogin()) {
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }
            $this->_experationYears();
            if (isset ($_POST['payTotal'] ) ) {
                $screenTotal = $_POST['payTotal'];
            } else {
                $screenTotal = $_POST['payTotal1'];
            }
             $interface->assign('totalFines',  $screenTotal );
            // Pay Items
            if (isset($_POST['cancelPayment']))  {
                header ('Location: Fines' );
                die();
            }
            $screenToShow = 'payment.tpl';
            $pageTitle = 'My Payment Information';
            if (isset($_POST['processPayment'])) {
                $paymet = array();
                for ($i = 0; $i <  count($_POST['pay_billID']); $i++ ) {
                    $line['billID']  = $_POST['pay_billID'][$i];
                    $line['title'] =  $_POST['pay_title'][$i];
                    $line['fine'] =  $_POST['pay_fineType'][$i]; 
                    $line['amount'] =  $_POST['fineAmount'][$i];
                    $line['balance'] =  $_POST['balAmount'][$i];
                    $line['payment'] =  $_POST['payAmount'][$i];
                    $payment[] = $line;
                }
                if ($_POST['contribTotal'] > 0 ) {
                    $line['billID']  = '';
                    $line['title'] =   $_POST['contribName'];
                    $line['amount'] = '';
                    $line['balance'] =  '';
                    $line['payment'] =  $_POST['contribTotal'];
                    $line['fine'] =  'contrib'; 
                    $payment[] = $line;
                }
                $_SESSION['payments'] = $payment;
            }
            
            if (isset($_POST['continuePayment']))  {
                if ((isset($_POST['confirmPayment'])) && ($_POST['confirmPayment'] == 1 )) {
                    $_POST['cardType']      = $_SESSION['card']['cardType'];
                    $_POST['cardMonth']     = $_SESSION['card']['cardMonth'];
                    $_POST['cardYear']      = $_SESSION['card']['cardYear'];
                    $_POST['cardNumber']    = $_SESSION['card']['cardNumber'];
                    $_POST['card-ccv']      = $_SESSION['card']['card-ccv'];
                    $_POST['card-name']     = $_SESSION['card']['card-name'];
                    $_POST['addressLine1']  = $_SESSION['card']['addressLine1']; 
                    $_POST['addressLine2']  = $_SESSION['card']['addressLine2'];
                    $_POST['addressCity']   = $_SESSION['card']['addressCity'];
                    $_POST['addressState']  = $_SESSION['card']['addressState']; 
                    $_POST['addressZip']    = $_SESSION['card']['addressZip'];
                    $_POST['addressEmail']  = $_SESSION['card']['addressEmail'];
                    $_POST['payTotal1']     = $_SESSION['card']['payTotal1'];
                    unset($_SESSION['card']); 
                } else {
                    if ((! isset($_POST['cardType'])) || ($_POST['cardType'] == "Null" ) ) {
                        $errMsg = 'cardType';
                    } elseif ((! isset($_POST['cardNumber'])) || ($_POST['cardNumber'] == "" ) ) {
                        $errMsg = 'cardNumber';
                    } elseif ((! isset($_POST['card-ccv'])) || ($_POST['card-ccv'] == "" ) ) {
                        $errMsg = 'ccvNumber';
                    } elseif ((! isset($_POST['card-name'])) || ($_POST['card-name'] == "" ) ) {
                        $errMsg = 'cardName';
                    } elseif ((! isset($_POST['addressLine1'])) || ($_POST['addressLine1'] == "" ) ) {
                        $errMsg = 'cardAddress';
                    } elseif ((! isset($_POST['addressCity'])) || ($_POST['addressCity'] == "" ) ) {
                        $errMsg = 'cardCity';
                    } elseif ((! isset($_POST['addressState'])) || ($_POST['addressState'] == "" ) ) {
                        $errMsg = 'cardState';
                    } elseif ((! isset($_POST['addressZip'])) || ($_POST['addressZip'] == "" ) ) {
                        $errMsg = 'cardZip'; 
                    } elseif ((! isset($_POST['addressEmail'])) || ($_POST['addressEmail'] == "" ) ) {
                        $errMsg = 'cardEmail';
                    }
                }
                if ((isset($errMsg)) &&  ($errMsg == '' )) {
                    if (! $this->_validateCC($_POST['cardNumber'], $_POST['cardType'] )) {
                         $errMsg = 'cardNumberInvalid';
                    }
                }
                if ((isset($errMsg)) &&  ($errMsg != '' )) {
                    $interface->assign('errMsg', $errMsg);
                } else {
                    if ((isset($_POST['confirmPayment'])) && ($_POST['confirmPayment'] == 1 )) {
                        $payMsg = $this->_payFines($patron);
                        if ($payMsg['success'] != 1 ) {
                            $interface->assign('errMsg', $payMsg['error']);
                        } else {
                            $result = $this->_postPayment($patron, $_SESSION['payments'], $payMsg['transaction_id'], $creditCards[$_POST['cardType']] );
                            if ($result['success'] == 1 ) {
                                $location = $configArray['Site']['surl'] . "/MyResearch/Paid" . '?transID=' .  $payMsg['transaction_id'];
                                header("Location: ". $location );
                                die();
                            }
                        }
                    } else {
                        $card = array();
                        $card['cardType']       = $_POST['cardType'];
                        $card['cardNumber']     = $_POST['cardNumber'];
                        $card['cardMonth']      = $_POST['cardMonth']; 
                        $card['cardYear']       = $_POST['cardYear'];
                        $card['card-ccv']       = $_POST['card-ccv'];
                        $card['card-name']      = $_POST['card-name'];
                        $card['addressLine1']   = $_POST['addressLine1']; 
                        $card['addressLine2']   = $_POST['addressLine2']; 
                        $card['addressCity']    = $_POST['addressCity']; 
                        $card['addressState']   = $_POST['addressState']; 
                        $card['addressZip']     = $_POST['addressZip']; 
                        $card['addressEmail']   = $_POST['addressEmail'];
                        $card['payTotal1']      = $_POST['payTotal1']; 
                        $_SESSION['card'] = $card;
                        $interface->assign('ccard', $this->_maskCreditCard($_POST['cardNumber']) );
                        $interface->assign('ctype', $creditCards[$card['cardType']]  );
                        $interface->assign('confirmPayment', 1);
                        $interface->assign('rawFinesData', $_SESSION['payments']);
                        $screenToShow = 'fines.tpl';
                        $pageTitle = 'Confirm Payment';
                    }
                }
            } 
        }
        $interface->setTemplate($screenToShow);
        $interface->setPageTitle($pageTitle);
        $interface->display('layout.tpl');
    }
    private function _creditCardsAccepted($creditCards)
    {
        foreach ($creditCards as $key => $value ) { 
            $card= array();
            if (! isset($makeSelected ) ) {
                $makeSelected = 1;
                $card['selected'] = 1;
                $card['code'] = $key;
                $card['display'] = $value;
            } else { 
                $card['selected'] = 0;
                $card['code'] = $key;
                $card['display'] = $value;
            } 
            $cards[] = $card;   
        }
        return $cards;
    }

    
    private function _maskCreditCard($cc)
    {
        $cc_length = strlen($cc);
            // Replace all characters of credit card except the last four and dashes
        for($i=0; $i<$cc_length-4; $i++){
            if($cc[$i] == '-'){
                continue;
            }
            $cc[$i] = 'X';
        }
        return $cc;
    }

    private function _experationYears()
    {
        $currYear = date('Y');
        for ($i = 0; $i <10; $i++ ) {
            $return[] = $currYear + $i;
        }
        return $return;
    }
    private function _buildInitalData($result) {
        for ($i = 0; $i < count($result); $i++) {
            $row = &$result[$i];
            $record = $this->db->getRecord($row['id']);
            $row['title'] = $record ? $record['title_short'] : null;
        }   
        return $result;
    }  

    //  validateCC($number[,$type]) 
    //  Uses  the  MOD  10  algorythm  to  determine  if  a 
    //  credit  card  number  is  valid.                                                                               
 
    //  The  function  returns  true  if  the  CC  is    
    //  valid,  false  if  it  is  invalid.     
    //  the  type  entered  does  not  match  it's  supported   
    //  types.     
 
    private function _validateCC($ccnum, $type = 'unknown')
    { 
 
        //Clean  up  input 
        $type  =  strtolower($type); 
        $ccnum  =  preg_replace( '/\s/', '', $ccnum);   
 
        //Do  type  specific  checks 
        if  ($type  ==  'MO'){    // Master Card
            if  (strlen($ccnum)  !=  16  ||  !ereg( '^5[1-5]',  $ccnum))  { 
                return  0; 
            }
        } elseif  ($type  ==  'VO'){    // Visa
            if  ((strlen($ccnum)  !=  13  && 
                strlen($ccnum)  !=  16)  ||   substr($ccnum,  0,  1)  !=  '4') {
                return  0;
            } 
        } elseif  ($type  ==  'AO'){    // American Express 
            if  (strlen($ccnum)  !=  15  ||  !ereg( '^3[47]',  $ccnum))   {
                return 0; 
            }        
        }    
        elseif  ($type  ==  'DO'){      // Discover
            if  (strlen($ccnum)  !=  16  || substr($ccnum,  0,  4)  !=   '6011') { 
                return   0; 
            }
        } else  { 
            // al others we will assume are OK and send to Authoritize to check
            return  -1; 
        } 
 
        //  Start  MOD  10  checks 
 
        $dig  =  $this->_toCharArray($ccnum); 
        $numdig  =  sizeof  ($dig); 
        $j  =  0; 
        FOR  ($i=($numdig-2);  $i>=0;  $i-=2){ 
            $dbl[$j]  =  $dig[$i]  *  2; 
            $j++; 
        }         
        $dblsz  =  sizeof($dbl); 
        $validate  =0; 
        for  ($i=0;$i<$dblsz;$i++){ 
            $add  =  toCharArray($dbl[$i]); 
            for  ($j=0;$j<sizeof($add);$j++){ 
                $validate  +=  $add[$j]; 
            } 
            $add  =  ''; 
        } 
        for  ($i=($numdig-1);  $i>=0;  $i-=2){ 
            $validate  +=  $dig[$i];   
        } 
        if  (substr($validate,  -1,  1)  ==  '0') {
             return  1; 
        } else {
            return  0;
        } 
    } 
 
 
//  takes  a  string  and  returns  an  array  of  characters 
 
    private function  _toCharArray($input){ 
        $len  =  strlen($input); 
        for  ($j=0; $j<$len; $j++){ 
            $char[$j]  =  substr($input,  $j,  1);         
        } 
        return  ($char); 
    }    
 
    private function _buildPaymentResult($select, $result) {
        $newResult = array();
        for ($i = 0; $i < count($result); $i++) {
            $row = &$result[$i];
            if (in_array( $row['billID'], $select)) {
                $record = $this->db->getRecord($row['id']);
                $row['title'] = $record ? $record['title_short'] : null;
                $row['payment'] = $row['balance']; 
                $newResult[] = $row;
            }
        }   
        $row = array();
        $row['fine'] = 'total';
        $newResult[] = $row;
        return $newResult; 
    }

    /**
     * Private method for paying outstanding fines
     *
     * @param array $patron An array of patron information
     *
     * @return null
     * @access private
     */
    private function _payFines($patron)
    {
        $authorize =  $this->catalog->getAuthorizeNetInfo($patron);
        
        $path =  dirname(__FILE__);
        $p = strpos($path, '/web/' );
        $path = substr($path, 0, $p + 5); 

			// for testing set to use the sandbox and log transactions
        $authorize['sandbox'] = true;
        $authorize['logfile'] = $path . 'logging' . '/authorizeTrace' . date('Y_m_d', time())  . ".txt";;
        if (! $authorize['enabled'] ) { 
            return false;
        } 
        $_SESSION['authorize'] = $authorize;
        require_once  'services/MyResearch/lib/AuthorizeNet/shared/AuthorizeNetRequest.php';
        require_once  'services/MyResearch/lib/AuthorizeNet/shared/AuthorizeNetTypes.php';
        require_once  'services/MyResearch/lib/AuthorizeNet/shared/AuthorizeNetXMLResponse.php';
        require_once  'services/MyResearch/lib/AuthorizeNet/shared/AuthorizeNetResponse.php';
        require_once  'services/MyResearch/lib/AuthorizeNet/AuthorizeNetAIM.php';

        $name = trim($_POST['card_name']);
        $name = preg_replace('/\s\s+/', ' ', $name);
        $a = explode(' ', $name);
        if (count($a) > 2 ) {
            $first = $a[0];
            $last = $a[2];
        } else {
            $first = $a[0];
            $last = $a[1];
        }
        $fullAddress = $_POST['addressLine1'] .  $_POST['addressLine2']; 
        $transaction = new AuthorizeNetAIM;
        $transaction->setSandbox( $_SESSION['authorize']['sandbox']);
        $transaction->setFields(
            array(
                'amount' => $_POST['payTotal1'], 
                'card_num' => $_POST['cardNumber'], 
                'exp_date' => $_POST['cardMonth'] . '/' .$_POST['cardYear'],
                'first_name' => $first,
                'last_name' => $last,
                'address' => $fullAddress,
                'city' => $_POST['addressCity'],
                'state' => $_POST['addressState'],
                'zip' => $_POST['addressZip'],
                'email' => $_POST['addressEmail'],
                'card_code' => $_POST['card_ccv'],
            )
        );
        $payments = $_SESSION['payments'];
        foreach ($payments as $key => $line ) {
            if ($line['fine'] == 'contrib' ) {
                $type = 'Contribution';
                $title = 'to your library';
            } elseif ($line['fine'] == 'Billing Fee' ) {
                $type = 'Billing Fee';
                $title = 'for overdue items';
            } else {
                $type = 'Overdue Fine';
                $title = substr($line['title'], 0, 60);
                $p = strrpos($title, '/' );
                if ($p !== false ) {
                    $title = substr($title, 0, $p);
                }
            }
            $transaction->addLineItem( $key, $type,  $title, 1, $line['payment'], 'N');
        }
        $response = $transaction->authorizeAndCapture();
        if ($response->approved) {
            // Transaction approved! Do your logic here.
            $line['success'] = 1;
            $line['error'] = '';
            $line['transaction_id'] = $response->transaction_id;
        } else {
            $line['success'] = 0;
            $line['transaction_id'] = '';
            $line['error'] = 'transaction failed,  response_reason_code='. $response->response_reason_code
             . ' response_code= ' . $response->response_code 
             . ' response_reason_text = ' . $response->response_reason_text;
        }
        return $line;
    }

    private function _postPayment( $patron, $payments, $transactionId, $cardType) 
    {
    
        $authorize =  $this->catalog->postCreditPayment($patron, $payments, $transactionId, $cardType);
        return $authorize;

    }
    
}

?>
