<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config locale allowed currencies backend
 */
namespace Magento\Backend\Model\Config\Backend;

class Locale extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Core\Model\Resource\Config\Data\CollectionFactory
     */
    protected $_configsFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configsFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configsFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configsFactory = $configsFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_storeFactory = $storeFactory;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _afterSave()
    {
        /** @var $collection \Magento\Core\Model\Resource\Config\Data\Collection */
        $collection = $this->_configsFactory->create();
        $collection->addPathFilter('currency/options');

        $values = explode(',', $this->getValue());
        $exceptions = array();

        foreach ($collection as $data) {
            $match = false;
            $scopeName = __('Default scope');

            if (preg_match('/(base|default)$/', $data->getPath(), $match)) {
                if (!in_array($data->getValue(), $values)) {
                    $currencyName = $this->_localeCurrency->getCurrency($data->getValue())->getName();
                    if ($match[1] == 'base') {
                        $fieldName = __('Base currency');
                    } else {
                        $fieldName = __('Display default currency');
                    }

                    switch ($data->getScope()) {
                        case \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT:
                            $scopeName = __('Default scope');
                            break;

                        case \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE:
                            /** @var $website \Magento\Store\Model\Website */
                            $website = $this->_websiteFactory->create();
                            $websiteName = $website->load($data->getScopeId())->getName();
                            $scopeName = __('website(%1) scope', $websiteName);
                            break;

                        case \Magento\Store\Model\ScopeInterface::SCOPE_STORE:
                            /** @var $store \Magento\Store\Model\Store */
                            $store = $this->_storeFactory->create();
                            $storeName = $store->load($data->getScopeId())->getName();
                            $scopeName = __('store(%1) scope', $storeName);
                            break;
                    }

                    $exceptions[] = __('Currency "%1" is used as %2 in %3.', $currencyName, $fieldName, $scopeName);
                }
            }
        }
        if ($exceptions) {
            throw new \Magento\Framework\Model\Exception(join("\n", $exceptions));
        }

        return $this;
    }
}
