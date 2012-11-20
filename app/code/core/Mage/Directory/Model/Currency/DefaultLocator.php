<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Directory_Model_Currency_DefaultLocator
{
    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_App $app
     */
    public function __construct(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Retrieve default currency for selected store, website or website group
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return string
     */
    public function getDefaultCurrency(Mage_Core_Controller_Request_Http $request)
    {
        if ($request->getParam('store')) {
            $store = $request->getParam('store');
            $currencyCode = $this->_app->getStore($store)->getBaseCurrencyCode();
        } else if ($request->getParam('website')) {
            $website = $request->getParam('website');
            $currencyCode = $this->_app->getWebsite($website)->getBaseCurrencyCode();
        } else if ($request->getParam('group')) {
            $group = $request->getParam('group');
            $currencyCode =  $this->_app->getGroup($group)->getWebsite()->getBaseCurrencyCode();
        } else {
            $currencyCode = $this->_app->getStore()->getBaseCurrencyCode();
        }

        return $currencyCode;
    }
}