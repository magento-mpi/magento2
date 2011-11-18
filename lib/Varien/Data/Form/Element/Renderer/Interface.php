<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form field renderer
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element);
}
