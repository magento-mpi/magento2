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
namespace Magento\Catalog\Model\Config\Source\Product\Options;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/product/options/custom/groups';

    public function toOptionArray()
    {
        $groups = array(
            array('value' => '', 'label' => __('-- Please select --'))
        );

        foreach (\Mage::getConfig()->getNode(self::PRODUCT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = array();
            $typesPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach (\Mage::getConfig()->getNode($typesPath)->children() as $type) {
                if (isset($type->disabled) && (string)$type->disabled) {
                    continue;
                }
                $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName()
                    . '/label';
                $types[] = array(
                    'label' => __((string) \Mage::getConfig()->getNode($labelPath)),
                    'value' => $type->getName()
                );
            }

            $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            if (count($types)) {
                $groups[] = array(
                    'label' => __((string) \Mage::getConfig()->getNode($labelPath)),
                    'value' => $types
                );
            }
        }

        return $groups;
    }
}
