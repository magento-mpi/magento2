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
 * Remove button for template block for Visual Design Editor
 *
 * @method string getWrappedElementId()
 */
class Mage_DesignEditor_Block_Wrapper_Remove extends Mage_Core_Block_Template
{
    /**
     * Build remove button HTML id
     *
     * @return string
     */
    public function getElementId()
    {
        return $this->getWrappedElementId() . '_remove';
    }
}
