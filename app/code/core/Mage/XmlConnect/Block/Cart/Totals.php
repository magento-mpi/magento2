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
 * Shopping cart totals xml renderer
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
   /**
     * Render cart totals xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $quote = $this->getQuote();
        $totalsXmlObj  = new Varien_Simplexml_Element('<totals></totals>');

        foreach ($quote->getTotals() as $total) {
            $value = sprintf('%01.2f', $total->getValue());
            if ($value != 0.00 || $total->getCode() == 'subtotal' || $total->getCode() == 'grand_total' || $total->getCode() == 'shipping') {
                $totalXmlObj = $totalsXmlObj->addChild($total->getCode());
                $totalXmlObj->addChild('title', $totalsXmlObj->xmlentities(strip_tags($total->getTitle())));
                $formatedValue = $quote->getStore()->formatPrice($value, false);
                $totalXmlObj->addChild('value', $value);
                $totalXmlObj->addChild('formated_value', $formatedValue);
            }
        }

        return $totalsXmlObj->asNiceXml();
    }

}
