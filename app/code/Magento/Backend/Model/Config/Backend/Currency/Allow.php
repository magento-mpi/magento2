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
 * Config Directory currency backend model
 * Allows dispatching before and after events for each controller action
 */
namespace Magento\Backend\Model\Config\Backend\Currency;

class Allow extends AbstractCurrency
{
    /**
     * @var \Magento\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Locale\CurrencyInterface $localeCurrency,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_localeCurrency = $localeCurrency;
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $coreStoreConfig,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _afterSave()
    {
        $exceptions = array();
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = __(
                    'Selected allowed currency "%1" is not available in installed currencies.',
                    $this->_localeCurrency->getCurrency($currencyCode)->getName()
                );
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = __(
                'Default display currency "%1" is not available in allowed currencies.',
                $this->_localeCurrency->getCurrency($this->_getCurrencyDefault())->getName()
            );
        }

        if ($exceptions) {
            throw new \Magento\Model\Exception(join("\n", $exceptions));
        }

        return $this;
    }
}
