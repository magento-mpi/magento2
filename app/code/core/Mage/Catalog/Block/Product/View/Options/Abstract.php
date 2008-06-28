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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product options abstract type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_View_Options_Abstract extends Mage_Core_Block_Template
{
    protected $_option;

    protected $_priceIncludingTax;
    protected $_priceExcludingTax;

    /**
     * Set option
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Block_Product_View_Options_Abstract
     */
    public function setOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            $this->_priceIncludingTax = $option->getPriceIncludingTax();
            $this->_priceExcludingTax = $option->getPriceExcludingTax();
            return $this->_formatPrice(array(
                'is_percent' => ($option->getPriceType() == 'percent') ? true : false,
                'pricing_value' => $option->getPrice()
            ));
        }
        return '';
    }

    public function getPriceIncludingTax()
    {
        return $this->_priceIncludingTax;
        return $this->getOption()->getPriceIncludingTax();
    }

    public function getPriceExcludingTax()
    {
        return $this->_priceExcludingTax;
    }

    /**
     * Return formated price
     *
     * @param array $value
     * @return string
     */
    protected function _formatPrice($value)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }
        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }
//        if ($value['is_percent']) {
//            $priceStr = $sign . '%' . number_format($value['pricing_value'], 0, null, '');
//        } else {
            $priceStr = $sign;
            if (Mage::helper('tax')->displayPriceIncludingTax()) {
                $priceStr .= $this->helper('core')->currency($this->getPriceIncludingTax());
            } elseif (Mage::helper('tax')->displayPriceExcludingTax()) {
                $priceStr .= $this->helper('core')->currency($this->getPriceExcludingTax());
            } elseif (Mage::helper('tax')->displayBothPrices()) {
                $priceStr .= $this->helper('core')->currency($this->getPriceExcludingTax());
                $priceStr .= ' ('.$sign.$this->helper('core')->currency($this->getPriceIncludingTax()).' '.$this->__('Incl. Tax').')';
            }
//        }

        return '(' . $priceStr . ')';
    }
}