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
 *
 * @method string getElementName()
 * @method string getElementId()
 * @method string getElementHtml()
 * @method string getElementTitle()
 * @method bool getIsManipulationAllowed()
 * @method bool getIsContainer()
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

    /**
     * Get remove button for block/container wrapper
     *
     * @param string $elementId
     * @return string
     */
    public function getRemoveButton($elementId)
    {
        /** @var $block Mage_DesignEditor_Block_Wrapper_Remove */
        $block = Mage::getModel('Mage_DesignEditor_Block_Wrapper_Remove', array(
            'template'   => 'wrapper/remove.phtml',
            'wrapped_element_id' => $elementId
        ));
        return $block->toHtml();
    }
}
