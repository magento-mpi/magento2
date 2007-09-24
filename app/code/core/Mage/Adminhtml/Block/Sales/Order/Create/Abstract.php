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
 * Adminhtml sales order create abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Abstract extends Mage_Adminhtml_Block_Widget
{

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Model_Quote
     */
    protected $_session = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/create/abstract.phtml');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Adminhtml_Model_Quote $session
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Abstract
     */
    public function setSession($session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function getSession()
    {
        if (is_null($this->_session)) {
             $this->setSession(Mage::getSingleton('adminhtml/quote'));
        }
        return $this->_session;
    }

    /**
     *
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    public function getCustomerId()
    {
        return $this->getSession()->getCustomerId();
    }

    public function getIsOldCustomer()
    {
        return $this->getSession()->getIsOldCustomer();
    }

    public function getStoreId()
    {
        return $this->getSession()->getStoreId();
    }

    public function formatPrice($price)
    {
        return $this->getSession()->formatPrice($price);
    }

    public function getHeaderText()
    {
        return __('Header Text');
    }

    public function getHeaderCssClass()
    {
        return 'head-edit-form';
    }

    public function getScUrl($action = '')
    {
        if ($action) {
            return Mage::getUrl('*/*/' . $action);
        }
        return Mage::getUrl('*/*');
    }


    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getModel('directory/country')->getResourceCollection()
                ->load();
        }
        return $this->_countryCollection;
    }

    public function getRegionCollection($type)
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress($type)->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    public function customerHasAddresses()
    {
        if ($this->getIsOldCustomer() && $this->getQuote()->getCustomer()->getLoadedAddressCollection()->count()) {
            return true;
        }
        return false;
    }

    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getLoadedAddressCollection() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->getStreet(-1).', '.$address->getCity().', '.$address->getRegion().' '.$address->getPostcode(),
                );
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $addressId = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $addressId = $this->getCustomer()->getPrimaryShippingAddress();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', __('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.'_address:country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select input-text')
            ->setValue($this->getAddress($type)->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->setExtraParams('onchange="sc_countrySelect(this);"')
        ;
        if (('shipping' == $type) && ($this->getSession()->getSameAsBilling())) {
            $select->setExtraParams(' disabled');
        }
        return $select->getHtml();
    }


    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[region]')
            ->setId($type.'_address:region_id')
            ->setTitle(__('State/Province'))
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getAddress($type)->getRegionId())
            ->setOptions($this->getRegionCollection($type)->toOptionArray())
            ->setExtraParams('onchange="sc_regionSelect(this);"')
        ;
        if (('shipping' == $type) && ($this->getSession()->getSameAsBilling())) {
            $select->setExtraParams(' disabled');
        }
        return $select->getHtml();
    }

    public function getAddress($type) {
        if ($type == 'billing') {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }
        if (! $address) {
            $address = Mage::getModel('sale/quote_address');
        }
        return $address;
    }

}
