<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Entity\Product\Attribute\Design\Options;

/**
 * Entity/Attribute/Model - select product design options container from config
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\Eav\Model\Entity\Attribute\Source\Config
{
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        if (sizeof($options) > 0) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    return $option['label'];
                }
            }
        }
        if (isset($options[$value])) {
            return $option[$value];
        }
        return false;
    }
}
