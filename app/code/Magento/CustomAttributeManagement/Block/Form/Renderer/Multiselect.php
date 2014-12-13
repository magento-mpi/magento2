<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

/**
 * EAV Entity Attribute Form Renderer Block for multiply select
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Multiselect extends \Magento\CustomAttributeManagement\Block\Form\Renderer\Select
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getAttributeObject()->getSource()->getAllOptions();
    }

    /**
     * Return array of values
     *
     * @return array
     */
    public function getValues()
    {
        $value = $this->getValue();
        return explode(',', $value);
    }

    /**
     * Check is value selected
     *
     * @param string $value
     * @return boolean
     */
    public function isValueSelected($value)
    {
        return in_array($value, $this->getValues());
    }
}
