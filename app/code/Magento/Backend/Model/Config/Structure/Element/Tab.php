<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Tab
    extends Magento_Backend_Model_Config_Structure_Element_CompositeAbstract
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
