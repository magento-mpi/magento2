<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display color picker element for VDE
 *
 * @method string getValue()
 * @method string getExtType()
 * @method string getCssClass()
 * @method string getRequired()
 * @method string getNote()
 * @method \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\ColorPicker setCssClass($class)
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

class ColorPicker extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'color-picker';

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->setCssClass('element-' . self::CONTROL_TYPE);
    }
}
