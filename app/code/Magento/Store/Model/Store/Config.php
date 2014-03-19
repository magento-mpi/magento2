<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model\Store;

class Config implements \Magento\Store\Model\Store\ConfigInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\Resource\Store\Collection
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Store\Model\Resource\Store\CollectionFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Store\Model\Resource\Store\CollectionFactory $factory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Store\Model\Resource\Store\CollectionFactory $factory
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_factory = $factory;
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string|null
     */
    public function getConfig($path, $store = null)
    {
        return $this->_storeManager->getStore($store)->getConfig($path);
    }

    /**
     * Retrieve store config flag
     *
     * @param string $path
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function getConfigFlag($path, $store = null)
    {
        $flag = strtolower($this->getConfig($path, $store));
        return !empty($flag) && 'false' !== $flag;
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * If empty $allowValues then retrieve all stores values
     *
     * return array($storeId => $pathValue)
     *
     * @param string $path
     * @param array $allowedValues
     * @param string $keyAttribute
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getStoresConfigByPath($path, $allowedValues = array(), $keyAttribute = 'id')
    {
        if (is_null($this->_storeCollection)) {
            $this->_storeCollection = $this->_factory->create();
            $this->_storeCollection->setLoadDefault(true);
        }
        $storeValues = array();
        /** @var $store \Magento\Store\Model\Store */
        foreach ($this->_storeCollection as $store) {
            switch ($keyAttribute) {
                case 'id':
                    $key = $store->getId();
                    break;
                case 'code':
                    $key = $store->getCode();
                    break;
                case 'name':
                    $key = $store->getName();
                    break;
                default:
                    throw new \InvalidArgumentException("'{$keyAttribute}' cannot be used as a key.");
                    break;
            }

            $value = $this->_config->getValue($path, 'store', $store->getCode());
            if (empty($allowedValues)) {
                $storeValues[$key] = $value;
            } elseif (in_array($value, $allowedValues)) {
                $storeValues[$key] = $value;
            }
        }
        return $storeValues;
    }
}
