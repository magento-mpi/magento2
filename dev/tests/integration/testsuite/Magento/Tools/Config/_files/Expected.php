<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\Config;

class Test
{
    /**
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $storeConfig
    ) {
        $this->storeConfig = $storeConfig;
    }

    public function someFunction()
    {
        $store = new \Magento\Object;
        $this->_storeConfig->getValue(
            '1checkout/cart_link/use_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getId()
        );

        $this->coreStoreConfig->getValue('2checkout/cart_link/use_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());

        $this->storeConfig->getValue(CustomerGroupModel::XML_PATH_DEFAULT_ID1, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->storeConfig->getValue($this->qwe(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->storeConfig->getValue($this->asd('asd'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->_storeConfig->getValue($this->_serviceConfigPath . '/' . $key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore());

        $this->_storeConfig->getValue(
            $this->getXml(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
        $this->_storeConfig->isSetFlag(
            $this->getXml(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getId()
        );

        if ($this->_storeConfig->getValue(self::XML_PATH_USE_HTTP_VIA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]
        ) {
            return false;
        }

        $this->_storeConfig
            ->getValue(
                $this->getXml(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );

        $redirectCode = (int)$this->_storeConfig->getValue('web/url/redirect_to_base', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) !== 301
            ? 302
            : 301;
    }
}