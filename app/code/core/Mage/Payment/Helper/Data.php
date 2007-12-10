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
 
/**
 * Payment module base helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PAYMENT_METHODS = 'payment';
    
    /**
     * Retrieve payment method model object
     *
     * @param   Varien_Simplexml_Element $config
     * @return  Mage_Payment_Model_Abstract
     */
    protected function _getMethodInstance($config, $code)
    {
        return Mage::getModel($config->getClassName())
            ->setCode($code);
    }
    
    /**
     * Retrieve available payment methods for store
     * 
     * array structure:
     *  $index => Varien_Simplexml_Element
     * 
     * @param   mixed $store
     * @return  array
     */
    public function getStoreMethods($store=null)
    {
        if (is_null($store)) {
            $methods = Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS);
        }
        elseif ($store instanceof Mage_Core_Model_Store){
            $methods = Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS, $store->getId());
        }
        else {
            $methods = Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS, $store);
        }
        
        $res = array();
        foreach ($methods as $code => $methodConfig) {
            if (!$methodConfig->is('active', 1)) {
                continue;
            }
            
            $methodInstance = $this->_getMethodInstance($methodConfig, $code);
            if (!isset($res[(int)$methodConfig->sort_order])) {
                $res[(int)$methodConfig->sort_order] = $methodInstance;
            }
            else {
                $res[] = $methodInstance;
            }
        }
        ksort($res);
        return $res;
    }
    
    /**
     * Retreive payment method form html
     *
     * @param   Mage_Payment_Model_Abstract $method
     * @return  string
     */
    public function getMethodForm(Mage_Payment_Model_Abstract $method)
    {
        /**
         * @todo declare method block and template information in layout xml
         */
        return $method->createFormBlock('payment.methods.'.$method->getCode())
            ->setPaymentMethod($method)
            ->toHtml();
    }
    
    /**
     * Retrieve formated payment method information
     * 
     * @todo    remove dependency from createInfoBlock method
     * @param   Mage_Payment_Model_Info $payment
     * @package string $format
     * @return  string
     */
    public function formatInfo(Mage_Payment_Model_Info $payment, $format=null)
    {
        $out = '';
        if ($methodCode = $payment->getMethod()) {
            $methodConfig = new Varien_Object(Mage::getStoreConfig('payment/'.$methodCode, $payment->getStoreId()));
            if ($methodConfig) {
                $className = $methodConfig->getModel();
                $method = Mage::getModel($className);
                if ($method) {
                    $out = '<p>'.$methodConfig->getTitle().'</p>';
                    $method->setPayment($payment);
                    $methodBlock = $method->createInfoBlock('payment.method.'.$methodCode.'.'.$payment->getId());
                    if (!empty($methodBlock)) {
                        $out .= $methodBlock->toHtml();
                    }
                }
            }
        }
        return $out;
    }
}