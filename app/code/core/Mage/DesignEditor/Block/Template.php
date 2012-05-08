<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extended template block for Visual Design Editor
 */
class Mage_DesignEditor_Block_Template extends Mage_Core_Block_Template
{
    /**
     * Check whether highlighting of elements is disabled or not
     *
     * @return bool
     */
    public function isHighlightingDisabled()
    {
        return Mage::getSingleton('Mage_DesignEditor_Model_Session')->isHighlightingDisabled();
    }
}
