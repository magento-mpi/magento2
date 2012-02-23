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
 * Block that wraps html with block info, so later it will be shown as draggable frontend block
 *
 * @method Mage_DesignEditor_Block_Wrapping setWrappedHtml($html)
 * @method Mage_DesignEditor_Block_Wrapping setWrappedBlock($block)
 * @method string|null getWrappedHtml()
 */
class Mage_DesignEditor_Block_Wrapping extends Mage_Core_Block_Template
{
    /**
     * Path to template file in theme
     *
     * @var string
     */
    protected $_template = 'wrapping.phtml';

    /**
     * Returns block, whose content is wrapped. The method is declared explicitly to ensure the block existence.
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getWrappedBlock()
    {
        $block = $this->_getData('wrapped_block');
        if (!($block instanceof Mage_Core_Block_Abstract)) {
            throw new Mage_DesignEditor_Exception(
                $this->__("Design Editor's wrapping method is called without providing valid block instance")
            );
        }
        return $block;
    }
}
