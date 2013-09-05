<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form password element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Password extends Magento_Data_Form_Element_Abstract
{
    /**
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param array $attributes
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $factoryElement,
        $attributes = array()
    ) {
        parent::__construct($factoryElement, $attributes);
        $this->setType('password');
        $this->setExtType('textfield');
    }
    
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }
}