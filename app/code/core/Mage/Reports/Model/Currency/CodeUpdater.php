<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Currency Code updater class
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Currency_CodeUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    public function __construct(Mage_Core_Controller_Request_Http $request = null)
    {
        $this->_request = (null === $request)? Mage::app()->getFrontController()->getRequest() : $request;
    }

    public function update($argument)
    {
        if ($this->_request->getParam('store')) {
            $store = $this->_request->getParam('store');
            $argument = Mage::app()->getStore($store)->getBaseCurrencyCode();
        } else if ($this->_request->getParam('website')){
            $website = $this->_request->getParam('website');
            $argument = Mage::app()->getWebsite($website)->getBaseCurrencyCode();
        } else if ($this->_request->getParam('group')){
            $group = $this->_request->getParam('group');
            $argument =  Mage::app()->getGroup($group)->getWebsite()->getBaseCurrencyCode();
        } else {
            $argument = Mage::app()->getStore()->getBaseCurrencyCode();
        }

        return $argument;
    }

}

