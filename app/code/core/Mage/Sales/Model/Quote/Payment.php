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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote payment information
 */
class Mage_Sales_Model_Quote_Payment extends Mage_Payment_Model_Info 
{
    protected $_eventPrefix = 'sales_quote_payment';
    protected $_eventObject = 'payment';
    
    protected $_quote;

    function _construct()
    {
        $this->_init('sales/quote_payment');
    }

    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    public function getQuote()
    {
        return $this->_quote;
    }

    public function importCustomerPayment(Mage_Customer_Model_Payment $payment)
    {
        $this
            ->setCustomerPaymentId($payment->getId())
            ->setMethod($payment->getMethod())
            ->setCcType($payment->getCcType())
            ->setCcNumberEnc($payment->getCcNumberEnc())
            ->setCcLast4($payment->getCcLast4())
            ->setCcCidEnc($payment->getCcCidEnc())
            ->setCcOwner($payment->getCcOwner())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
        ;
    }
    
    public function importOrderPayment($payment)
    {
        $this->setMethod($payment->getMethod())
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcLast4($payment->getCcNumber())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
            ->setCcNumberEnc($payment->getCcNumber())
            ->setCcCidEnc($payment->getCcCid())
            ->setCcType($payment->getCcNumber());

        return $this;
    }

    public function importPostData(array $data)
    {
        $payment = Mage::getModel('customer/payment')->setData($data);    
        
        $this
            ->setMethod($payment->getMethod())
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcLast4(substr($payment->getCcNumber(), -4))
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear());
            
        if($payment->getCcNumber()){
            $this->setCcNumberEnc($payment->encrypt($payment->getCcNumber()));
        }

        if($payment->getCcCid()){
            $this->setCcCidEnc($payment->encrypt($payment->getCcCid()));
        }
       
#print_r($this->getCcType());
#print_r($data);
        if (!$this->getCcType()) {
            $types = array(3=>__('American Express'), 4=>__('Visa'), 5=>__('Master Card'), 6=>__('Discover'));
            if (isset($types[(int)substr($payment->getCcNumber(),0,1)])) {
                $this->setCcType($types[(int)substr($payment->getCcNumber(),0,1)]);
            }
        }
#var_dump($this->getMethodInstance());

        //to validate post payment information        
        $this->getMethodInstance()->validateInfo($this);        
        
        return $this;
    }    
   
    
    public function getCheckoutRedirectUrl()
    {
        if (!($method = $this->getMethod())
            || !($modelName = Mage::getStoreConfig('payment/'.$method.'/model'))
            || !($model = Mage::getModel($modelName))) {
            return false;
        }

        return $model->getCheckoutRedirectUrl();
    }
}