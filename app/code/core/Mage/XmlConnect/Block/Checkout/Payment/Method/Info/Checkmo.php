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
 * Check / Money order Payment info xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Checkmo extends Mage_Payment_Block_Info_Checkmo
{
    /**
     * Prevent any rendering
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Add Check / Money order info to order XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $orderItemXmlObj->addAttribute('type', $this->getMethod()->getCode());
        $orderItemXmlObj->addAttribute('title', $this->getMethod()->getTitle());

        if ($this->getInfo()->getAdditionalData()) {
            if ($this->getPayableTo()) {
                $orderItemXmlObj->addCustomChild(
                    'item',
                    $this->htmlEscape($this->getPayableTo()),
                    array(
                        'label' => Mage::helper('sales')->__('Make Check payable to:')
                    )
                );
            }
            if ($this->getMailingAddress()) {
                $orderItemXmlObj->addCustomChild(
                    'item',
                    $this->htmlEscape($this->getMailingAddress()),
                    array(
                        'label' => Mage::helper('payment')->__('Send Check to:')
                    )
                );
            }
        }
    }
}
