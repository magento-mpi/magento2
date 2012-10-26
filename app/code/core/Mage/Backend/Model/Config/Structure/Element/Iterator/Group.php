<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Section_Iterator
    extends Mage_Backend_Model_Config_Structure_Element_IteratorAbstract
{
    /**
     * @param Mage_Backend_Model_Config_Structure_Tab $tab
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Tab $tab)
    {
        parent::__construct($tab);
    }
}
