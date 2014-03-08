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
 * EAV Entity Attribute Form Renderer Block for select
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

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
