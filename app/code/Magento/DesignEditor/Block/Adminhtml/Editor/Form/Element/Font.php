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
 * Form element renderer to display composite font element for VDE
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

class Font
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Composite\AbstractComposite
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'font';

    /**
     * Add form elements
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Font
     */
    protected function _addFields()
    {
        $fontData = $this->getComponent('font-picker');
        $colorData = $this->getComponent('color-picker');

        $fontHtmlId = $this->getComponentId('font-picker');
        $fontTitle = $this->_escape(sprintf("%s {%s: %s}",
            $fontData['selector'],
            $fontData['attribute'],
            $fontData['value']
        ));
        $this->addField($fontHtmlId, 'font-picker', array(
            'name'    => $fontHtmlId,
            'value'   => $fontData['value'],
            'title'   => $fontTitle,
            'options' => array_combine($fontData['options'], $fontData['options']),
            'label'   => null,
        ));

        $colorTitle = $this->_escape(sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        ));
        $colorHtmlId = $this->getComponentId('color-picker');
        $this->addField($colorHtmlId, 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'label' => null,
        ));

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Font
     */
    protected function _addElementTypes()
    {
        $this->addType('color-picker', 'Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\ColorPicker');
        $this->addType('font-picker', 'Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\FontPicker');

        return $this;
    }
}
