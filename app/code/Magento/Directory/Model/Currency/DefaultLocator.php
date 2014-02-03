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
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_app = $app;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve default currency for selected store, website or website group
     *
     * @param \Magento\App\RequestInterface $request
     * @return string
     */
    public function getDefaultCurrency(\Magento\App\RequestInterface $request)
    {
        if ($request->getParam('store')) {
            $store = $request->getParam('store');
            $currencyCode = $this->_storeManager->getStore($store)->getBaseCurrencyCode();
        } else if ($request->getParam('website')) {
            $website = $request->getParam('website');
            $currencyCode = $this->_storeManager->getWebsite($website)->getBaseCurrencyCode();
        } else if ($request->getParam('group')) {
            $group = $request->getParam('group');
            $currencyCode =  $this->_storeManager->getGroup($group)->getWebsite()->getBaseCurrencyCode();
        } else {
            $currencyCode = $this->_app->getBaseCurrencyCode();
        }

        return $currencyCode;
    }
}
