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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create shipping method form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Michael Bessolov <michael@varien.com>
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_rates;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_shipping_method_form');
        $this->setTemplate('sales/order/create/shipping/method/form.phtml');
    }

    /**
     * Retrieve quote shipping address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    /**
     * Retrieve array of shipping rates groups
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter($this->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                	$groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            return $this->_rates = $groups;
        }
        return $this->_rates;
    }
    
    /**
     * Rertrieve carrier name from store configuration
     *
     * @param   string $carrierCode
     * @return  string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $this->getStore()->getId())) {
            return $name;
        }
        return $carrierCode;
    }
    
    /**
     * Retrieve current selected shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }
    
    /**
     * Check activity of method by code
     *
     * @param   string $code
     * @return  bool
     */
    public function isMethodActive($code)
    {
        return $code===$this->getShippingMethod();
    }
    
    /**
     * Retrieve rate of active shipping method
     *
     * @return Mage_Sales_Model_Quote_Address_Rate || false
     */
    public function getActiveMethodRate()
    {
        $rates = $this->getShippingRates();
        if (is_array($rates)) {
            foreach ($rates as $group) {
            	foreach ($group as $code => $rate) {
            		if ($rate->getCode() == $this->getShippingMethod()) {
            		    return $rate;
            		}
            	}
            }
        }
        return false;
    }
}
