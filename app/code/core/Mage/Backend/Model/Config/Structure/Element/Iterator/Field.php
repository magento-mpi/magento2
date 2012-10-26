<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Field_Iterator
    extends Mage_Backend_Model_Config_Structure_Element_IteratorAbstract
{
    /**
     * @param Mage_Backend_Model_Config_Structure_Field $field
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Field $field)
    {
        parent::__construct($field);
    }

    /**
     * Check whether there are field children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }
}
