<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Payment_Model_Cc extends Mage_Payment_Model_Abstract
{
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/form_cc', $name)
            ->setPayment($this->getPayment())
            ->setMethod('ccsave');
        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setTemplate('payment/info/ccsave.phtml')
            ->setPayment($this->getPayment());
        return $block;
    }
    
    public function validateCcNum($cc_number)
    {
      $cardNumber = strrev($cc_number);
      $numSum = 0;

      for ($i=0; $i<strlen($cardNumber); $i++) {
        $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
        if ($i % 2 == 1) {
          $currentNum *= 2;
        }

// Add digits of 2-digit numbers together
        if ($currentNum > 9) {
          $firstNum = $currentNum % 10;
          $secondNum = ($currentNum - $firstNum) / 10;
          $currentNum = $firstNum + $secondNum;
        }

        $numSum += $currentNum;
      }

// If the total has no remainder it's OK
      return ($numSum % 10 == 0);
    }
    
     /*
    * validate cc type and cc number match or not    
    */
    public function validateInfo(Mage_Payment_Model_Info $info)
    {

/*
echo "<pre>Info:";    
echo $info->getCcNumber();    
echo $info->getCcType(); 
echo $info->getMethod();
print_r($this->getConfigData('cctypes'));
*/

//var_dump($info->getData());
         $errorMsg='';
         $availableTypes=explode(',',$this->getConfigData('cctypes'));         
         $cc_number=$info->getCcNumber();
         $cc_type='';
         
         if(in_array($info->getCcType(), $availableTypes)){
             if($this->validateCcNum($cc_number)){
                  if (ereg('^4[0-9]{12}([0-9]{3})?$', $cc_number)) {
                    $cc_type = 'VI';
                  } elseif (ereg('^5[1-5][0-9]{14}$', $cc_number)) {
                    $cc_type = 'MC';
                  } elseif (ereg('^3[47][0-9]{13}$', $cc_number)) {
                    $cc_type = 'AE';
                  } elseif (ereg('^6011[0-9]{12}$', $cc_number)) {
                   $cc_type = 'DI';
                  }
                  if($cc_type!=$info->getCcType()){
                    $errorMsg=__('Credit card number mismatch with credit card type');                              
                  }
             }else{
               $errorMsg=__('Invalid Credit Card Number');               
             }
             
         }else{
           $errorMsg=__('Credit card type is not allowed for this payment method');             
         }

         if($errorMsg){
            Mage::throwException($errorMsg);
         }
               
         return $this;
    }

}