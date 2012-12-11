<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Iterator_Group
    extends Mage_Backend_Model_Config_Structure_Element_Iterator
{
    /**
     * @param Mage_Backend_Model_Config_Structure_Element_Group $element
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Element_Group $element)
    {
        parent::__construct($element);
    }
}
