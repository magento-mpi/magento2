<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Iterator_Section
    extends Mage_Backend_Model_Config_Structure_Element_Iterator
{
    /**
     * @param Mage_Backend_Model_Config_Structure_Element_Section $element
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Element_Section $element)
    {
        parent::__construct($element);
    }
}
