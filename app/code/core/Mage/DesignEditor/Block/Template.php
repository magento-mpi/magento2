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
 * @method string getElementTitle()
 * @method bool getIsManipulationAllowed()
 * @method bool getIsContainer()
 */
class Mage_DesignEditor_Block_Template extends Mage_Core_Block_Template
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'wrapping.phtml';

    /**
     * Get remove button for block/container wrapper
     *
     * @param string $elementId
     * @return string
     */
    public function getRemoveButton($elementId)
    {
        /** @var $block Mage_DesignEditor_Block_Wrapper_Remove */
        $block = $this->getLayout()->createBlock('Mage_DesignEditor_Block_Wrapper_Remove', '',
            array(
                'data' => array(
                    'template'           => 'wrapper/remove.phtml',
                    'wrapped_element_id' => $elementId
                )
            )
        );
        return $block->toHtml();
    }

    /**
     * Get element html (real content or placeholder)
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementHtml = $this->getData('element_html');

        if (empty($elementHtml)) {
            /** @var $block Mage_DesignEditor_Block_Placeholder */
            $block = $this->getLayout()->createBlock('Mage_DesignEditor_Block_Placeholder');
            $elementHtml = $block->toHtml();
        }

        return $elementHtml;
    }
}
