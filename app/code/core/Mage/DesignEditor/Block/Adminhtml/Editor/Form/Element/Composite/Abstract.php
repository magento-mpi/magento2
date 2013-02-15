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
 * Parent composite form element for VDE
 *
 * @method array getComponents()
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
    extends Varien_Data_Form_Element_Fieldset
{
    /**
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory
     */
    protected $_rendererFactory;

    /**
     * @param Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory $rendererFactory
     * @param array $attributes
     */
    public function __construct(
        Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory $rendererFactory,
        $attributes = array()
    ) {
        
        echo __FILE__.':'.__LINE__;
        echo '<pre>';
        var_dump($rendererFactory, $attributes);
        echo '</pre>';
        exit;
        
         
        parent::__construct($attributes);
        $this->_rendererFactory = $rendererFactory;
    }
}

