<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for multiply select
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

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
