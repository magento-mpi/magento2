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

    public function toOptionArray()
    {
        $types = array();

        foreach (\Mage::getConfig()->getNode(self::BUNDLE_OPTIONS_TYPES_PATH)->children() as $type) {
            $labelPath = self::BUNDLE_OPTIONS_TYPES_PATH . '/' . $type->getName() . '/label';
            $types[] = array(
                'label' => (string) \Mage::getConfig()->getNode($labelPath),
                'value' => $type->getName()
            );
        }

        return $types;
    }
}
