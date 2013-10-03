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
 * Composite 'font' element renderer
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer;

class Font
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Magento_DesignEditor::editor/form/renderer/font.phtml';

    /**
     * Get element CSS classes
     *
     * @return array
     */
    public function getClasses()
    {
        /** @var $element \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Font */
        $element = $this->getElement();

        $classes = array();
        $classes[] = 'fieldset';
        if ($element->getClass()) {
            $classes[] = $element->getClass();
        }

        return $classes;
    }
}
