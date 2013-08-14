<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Iterator_Tab
    extends Magento_Backend_Model_Config_Structure_Element_Iterator
{
    /**
     * @param Magento_Backend_Model_Config_Structure_Element_Tab $element
     */
    public function __construct(Magento_Backend_Model_Config_Structure_Element_Tab $element)
    {
        parent::__construct($element);
    }
}
