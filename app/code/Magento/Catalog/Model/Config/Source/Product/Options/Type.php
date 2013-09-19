<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product option types mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Source_Product_Options_Type implements Magento_Core_Model_Option_ArrayInterface
{
    const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/product/options/custom/groups';

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
        $groups = array(
            array('value' => '', 'label' => __('-- Please select --'))
        );

        foreach ($this->_coreConfig->getNode(self::PRODUCT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = array();
            $typesPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach ($this->_coreConfig->getNode($typesPath)->children() as $type) {
                if (isset($type->disabled) && (string)$type->disabled) {
                    continue;
                }
                $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName()
                    . '/label';
                $types[] = array(
                    'label' => __((string) $this->_coreConfig->getNode($labelPath)),
                    'value' => $type->getName()
                );
            }

            $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            if (count($types)) {
                $groups[] = array(
                    'label' => __((string) $this->_coreConfig->getNode($labelPath)),
                    'value' => $types
                );
            }
        }

        return $groups;
    }
}
