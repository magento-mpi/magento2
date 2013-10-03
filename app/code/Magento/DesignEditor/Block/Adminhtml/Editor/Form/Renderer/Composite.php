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
 * Composite form element renderer
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer;

class Composite
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer\Recursive
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     * This templates are made of fieldset.phtml but split into several templates
     *
     * @var array
     */
    protected $_templates = array(
        'Magento_DesignEditor::editor/form/renderer/composite/children.phtml',
        'Magento_DesignEditor::editor/form/renderer/composite.phtml',
        'Magento_DesignEditor::editor/form/renderer/composite/wrapper.phtml',
    );

    /**
     * Get CSS classes for element
     *
     * Used in composite.phtml
     *
     * @return array
     */
    public function getCssClasses()
    {
        /** @var $element \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Composite\AbstractComposite */
        $element = $this->getElement();
        $isField = $element->getFieldsetType() == 'field';

        $cssClasses = array();
        $cssClasses[] = ($isField) ? 'field' : 'fieldset';
        if ($element->getClass()) {
            $cssClasses[] = $element->getClass();
        }
        if ($isField && $element->hasAdvanced()) {
            $cssClasses[] = 'complex';
        }

        return $cssClasses;
    }
}
