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
namespace Magento\Bundle\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
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
