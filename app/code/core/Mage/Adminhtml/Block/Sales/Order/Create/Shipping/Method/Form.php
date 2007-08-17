<?php
/**
 * Adminhtml sales order create shipping method form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form extends Mage_Adminhtml_Block_Widget
{

    protected $_rates;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_shipping_method_form');
        $this->setTemplate('sales/order/create/shipping/method/form.phtml');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote()->getShippingAddress();
    }

    public function getStore()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote()->getStore();
    }

    public function getQuote()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote();
    }

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

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $this->getStore()->getId())) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

}
