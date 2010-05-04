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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
        $methodsXmlObj = new Varien_Simplexml_Element('<payment_methods></payment_methods>');

        $methodBlocks = $this->getChild();
        foreach ($methodBlocks as $block) {
            if (!$block) {
                continue;
            }
            $method = $block->getMethod();
            if (!$method) {
                continue;
            }
            if (!$this->_canUseMethod($method)) {
                continue;
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
        }

        return $methodsXmlObj->asNiceXml();
    }

    /**
     * Check and prepare payment method model
     *
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        if (!$method->canUseForMultishipping()) {
            return false;
        }
        return parent::_canUseMethod($method);
    }
}
