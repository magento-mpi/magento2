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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout payment methods xml renderer
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_List extends Mage_Payment_Block_Form_Container
{

    /**
     * Prevent parent set childs
     *
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Render payment methods xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $methodsXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<payment_methods></payment_methods>');

        $methodBlocks = $this->getChild();
        $usedCodes = array();

        foreach ($methodBlocks as $block) {
            if (!$block) {
                continue;
            }
            $code = $this->_addToXml($block, $methodsXmlObj, $usedCodes);
            if ($code !== false) {
                $usedCodes[] = $code; 
            }
            /*
             * adding all ccsave childs
             */
            if ($block instanceOf Mage_XmlConnect_Block_Checkout_Payment_Method_Ccsave) {
                $paymentMethodList =  Mage::helper('xmlconnect/payment')->getPaymentMethodCodeList();
                foreach ($paymentMethodList as $methodCode) {
                    if (in_array($methodCode, $usedCodes)) {
                        continue;
                    }
                    try {
                        $methodInstance = Mage::helper('payment')->getMethodInstance($methodCode);
                        if (is_subclass_of($methodInstance, 'Mage_Payment_Model_Method_Cc')) {
                            $block->setData('method', $methodInstance);
                            $code = $this->_addToXml($block, $methodsXmlObj, $usedCodes);
                            if ($code !== false) {
                                $usedCodes[] = $code;
                            }
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        }


        return $methodsXmlObj->asNiceXml();
    }

    /**
     * Create child payment method xml node 
     * @param  Mage_Core_Block_Template                     $block
     * @param  Mage_XmlConnect_Model_Simplexml_Element      $methodsXmlObj
     * @param  array                                        $used codes
     * @return string|bool
     */
    protected function _addToXml($block, $methodsXmlObj, $usedCodes)
    {
            $method = $block->getMethod();
            if (!$this->_canUseMethod($method) || in_array($method->getCode(), $usedCodes)) {
                return false;
            }
            $this->_assignMethod($method);

            $methodItemXmlObj = $methodsXmlObj->addChild('method');
            $methodItemXmlObj->addAttribute('post_name', 'payment[method]');

            $methodItemXmlObj->addAttribute('code', $method->getCode());
            $methodItemXmlObj->addAttribute('label', $methodsXmlObj->xmlentities(strip_tags($method->getTitle())));
            if ($this->getQuote()->getPayment()->getMethod() == $method->getCode()) {
                $methodItemXmlObj->addAttribute('selected', 1);
            }
            $block->addPaymentFormToXmlObj($methodItemXmlObj);
        return $method->getCode();
    }

    /**
     * Check and prepare payment method model
     *
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        if (!$method || !$method->canUseCheckout() || !$method->canUseForMultishipping() || !$this->isAvailable($method)) {
            return false;
        }
        return parent::_canUseMethod($method);
    }

    /**
     * Check whether payment method can be used
     * @param $method Mage_Payment_Model_Method_Abstract
     * @return bool
     */
    public function isAvailable($method)
    {
        return (bool)(int)$method->getConfigData('active', ($this->getQuote() ? $this->getQuote()->getStoreId() : null));
    }
}
