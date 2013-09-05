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
 * Config backend model for "Custom Admin URL" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Admin_Custom extends Magento_Core_Model_Config_Data
{
    const CONFIG_SCOPE                      = 'stores';
    const CONFIG_SCOPE_ID                   = 0;

    const XML_PATH_UNSECURE_BASE_URL        = 'web/unsecure/base_url';
    const XML_PATH_SECURE_BASE_URL          = 'web/secure/base_url';
    const XML_PATH_UNSECURE_BASE_LINK_URL   = 'web/unsecure/base_link_url';
    const XML_PATH_SECURE_BASE_LINK_URL     = 'web/secure/base_link_url';

    /**
     * Writer of configuration storage
     *
     * @var Magento_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Config_Storage_WriterInterface $configWriter,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configWriter = $configWriter;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Validate value before save
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Custom
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!empty($value) && substr($value, -2) !== '}}') {
            $value = rtrim($value, '/').'/';
        }

        $this->setValue($value);
        return $this;
    }

    /**
     * Change secure/unsecure base_url after use_custom_url was modified
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Custom
     */
    public function _afterSave()
    {
        $useCustomUrl = $this->getData('groups/url/fields/use_custom/value');
        $value = $this->getValue();

        if ($useCustomUrl == 1 && empty($value)) {
            return $this;
        }

        if ($useCustomUrl == 1) {
            $this->_configWriter->save(
                self::XML_PATH_SECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID
            );
            $this->_configWriter->save(
                self::XML_PATH_UNSECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID
            );
        }

        return $this;
    }
}
