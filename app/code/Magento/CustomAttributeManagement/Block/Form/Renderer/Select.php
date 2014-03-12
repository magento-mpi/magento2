<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

/**
 * EAV Entity Attribute Form Renderer Block for select
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Select extends \Magento\CustomAttributeManagement\Block\Form\Renderer\AbstractRenderer
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
}
