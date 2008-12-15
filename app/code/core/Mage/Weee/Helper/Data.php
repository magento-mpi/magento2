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
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WEEE data helper
 */
class Mage_Weee_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getPriceDisplayType()
    {
        return Mage::getStoreConfig('tax/weee/display');
    }

    public function getListPriceDisplayType()
    {
        return Mage::getStoreConfig('tax/weee/display_list');
    }

    public function getSalesPriceDisplayType()
    {
        return Mage::getStoreConfig('tax/weee/display_sales');
    }

    public function getAmount($product, $shipping = null, $billing = null, $website = null) {
        return Mage::getModel('weee/tax')->getWeeeAmount($product, $shipping, $billing, $website);
    }

    public function typeOfDisplay($product, $compareTo = null, $zone = null)
    {
        $type = 0;
        switch ($zone) {
            case 'product_view':
            $type = $this->getPriceDisplayType();
            break;

            case 'product_list':
            $type = $this->getListPriceDisplayType();
            break;

            case 'sales':
            $type = $this->getSalesPriceDisplayType();
            break;

            default:
            if (Mage::registry('current_product')) {
                $type = $this->getPriceDisplayType();
            } else {
                $type = $this->getListPriceDisplayType();
            }
            break;
        }

        if (is_null($compareTo)) {
            return $type;
        } else {
            return $type == $compareTo;
        }
    }

    public function getProductWeeeAttributes($product, $shipping = null, $billing = null, $website = null)
    {
        return Mage::getModel('weee/tax')->getProductWeeeAttributes($product, $shipping, $billing, $website);
    }

    public function getApplied($item)
    {
        return unserialize($item->getWeeeTaxApplied());
    }

    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    public function isDiscounted()
    {
        return Mage::getStoreConfigFlag('tax/weee/discount');
    }
}
