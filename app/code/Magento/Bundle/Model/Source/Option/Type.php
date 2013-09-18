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
 * Bundle Option Type Source Model
 *
 * @category   Magento
 * @package    Magento_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Model\Source\Option;

class Type
{
    const BUNDLE_OPTIONS_TYPES_PATH = 'global/catalog/product/options/bundle/types';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
    }

    public function toOptionArray()
    {
        $types = array();

        foreach ($this->_coreConfig->getNode(self::BUNDLE_OPTIONS_TYPES_PATH)->children() as $type) {
            $labelPath = self::BUNDLE_OPTIONS_TYPES_PATH . '/' . $type->getName() . '/label';
            $types[] = array(
                'label' => (string) $this->_coreConfig->getNode($labelPath),
                'value' => $type->getName()
            );
        }

        return $types;
    }
}
