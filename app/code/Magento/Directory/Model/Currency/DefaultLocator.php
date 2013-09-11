<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Model\Currency;

class DefaultLocator
{
    /**
     * Application object
     *
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\App $app
     */
    public function __construct(\Magento\Core\Model\App $app)
    {
        $this->_app = $app;
    }

    /**
     * Retrieve default currency for selected store, website or website group
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return string
     */
    public function getDefaultCurrency(\Magento\Core\Controller\Request\Http $request)
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
