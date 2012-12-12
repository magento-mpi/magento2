<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Tab
    extends Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
{

    /**
     * Check whether tab is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->hasChildren();
    }
}
