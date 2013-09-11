<?php
/**
 * Encrypted config field backend model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_Encrypted
    extends Magento_Core_Model_Config_Value
    implements Magento_Core_Model_Config_Data_BackendModelInterface
{
    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $helper
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $helper,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Decrypt value after loading
     *
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = $this->_helper->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        // don't change value, if an obscured value came
        if (preg_match('/^\*+$/', $this->getValue())) {
            $value = $this->getOldValue();
        }
        if (!empty($value) && ($encrypted = $this->_helper->encrypt($value))) {
            $this->setValue($encrypted);
        }
    }

    /**
     * Get & decrypt old value from configuration
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->_helper->decrypt(parent::getOldValue());
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $this->_helper->decrypt($value);
    }
}
