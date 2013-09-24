<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle helper
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Catalog_Model_ProductTypes_ConfigInterface
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Catalog_Model_ProductTypes_ConfigInterface $config
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Catalog_Model_ProductTypes_ConfigInterface $config
    ) {
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        $configData = $this->_config->getType('bundle');
        return isset($configData['allowed_selection_types']) ? $configData['allowed_selection_types'] : array();
    }
}
