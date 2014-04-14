<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\System\Config\Backend;

/**
 * Backend model for merchant country. Default country used instead of empty value.
 */
class MerchantCountry extends \Magento\Framework\App\Config\Value
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Substitute empty value with Default country.
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = $this->_storeManager->getWebsite(
                    $this->getWebsite()
                )->getConfig(
                    \Magento\Core\Helper\Data::XML_PATH_DEFAULT_COUNTRY
                );
            } else {
                $defaultCountry = $this->_coreData->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}
