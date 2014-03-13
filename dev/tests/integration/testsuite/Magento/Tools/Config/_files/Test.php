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
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $storeConfig
    ) {
        $this->storeConfig = $storeConfig;
    }

    public function someFunction()
    {
        $store = new \Magento\Object;
        $this->_coreStoreConfig->getConfig(
            '1checkout/cart_link/use_qty',
            $store->getId()
        );

        $this->coreStoreConfig->getConfig('2checkout/cart_link/use_qty', $store->getId());

        $this->storeConfig->getConfig(CustomerGroupModel::XML_PATH_DEFAULT_ID1);
        $this->storeConfig->getConfig($this->qwe());
        $this->storeConfig->getConfig($this->asd('asd'));

        $this->_coreStoreConfig->getConfig($this->_serviceConfigPath . '/' . $key, $this->getStore());

        $this->_storeConfig->getConfig(
            $this->getXml(),
            $store->getId()
        );
        $this->_storeConfig->getConfigFlag(
            $this->getXml(),
            $store->getId()
        );

        if ($this->_storeConfig->getConfig(self::XML_PATH_USE_HTTP_VIA)
            && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]
        ) {
            return false;
        }

        $this->_storeConfig
            ->getConfig(
                $this->getXml(),
                $store->getId()
            );

        $redirectCode = (int)$this->_storeConfig->getConfig('web/url/redirect_to_base') !== 301
            ? 302
            : 301;
    }
}
