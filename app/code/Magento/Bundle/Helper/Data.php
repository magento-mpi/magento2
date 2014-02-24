<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Helper;

/**
 * Bundle helper
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
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
