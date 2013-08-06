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
 * Block that renders Quick Styles tabs
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_AbstractTab
    extends Mage_Backend_Block_Widget_Form
{
    /**
     * Form factory for VDE "Quick Styles" tab
     *
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder
     */
    protected $_formBuilder;

    /**
     * Theme context
     *
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * Tab form HTML identifier
     *
     * @var string
     */
    protected $_formId = null;

    /**
     * Controls group which will be rendered on the tab form
     *
     * @var string
     */
    protected $_tab = null;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder $formBuilder
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder $formBuilder,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_formBuilder = $formBuilder;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_Header
     * @throws Mage_Core_Exception
     */
    protected function _prepareForm()
    {
        if (!$this->_formId || !$this->_tab) {
            throw new Mage_Core_Exception(
                __('We found an invalid block of class "%1". Please define the required properties.',
                    get_class($this))
            );
        }
        $form = $this->_formBuilder->create(array(
            'id'            => $this->_formId,
            'action'        => '#',
            'method'        => 'post',
            'tab'           => $this->_tab,
            'theme'         => $this->_themeContext->getStagingTheme(),
            'parent_theme'  => $this->_themeContext->getEditableTheme()->getParentTheme(),
        ));
        $form->setUseContainer(true);

        $this->setForm($form);

        parent::_prepareForm();
        return $this;
    }
}
