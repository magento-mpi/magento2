<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for merchant country. Default country used instead of empty value.
 */
namespace Magento\Paypal\Model\System\Config\Backend;

class MerchantCountry extends \Magento\Core\Model\Config\Value
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Substitute empty value with Default country.
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = \Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(\Magento\Core\Helper\Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = $this->_coreData->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}
