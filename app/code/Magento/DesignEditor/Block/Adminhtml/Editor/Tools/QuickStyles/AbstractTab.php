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
 * Block that renders Quick Styles tabs
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_AbstractTab
    extends Magento_Backend_Block_Widget_Form
{
    /**
     * Form factory for VDE "Quick Styles" tab
     *
     * @var Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder
     */
    protected $_formBuilder;

    /**
     * Theme context
     *
     * @var Magento_DesignEditor_Model_Theme_Context
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
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder $formBuilder
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder $formBuilder,
        Magento_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $formFactory, $data);
        $this->_formBuilder = $formBuilder;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_Header
     * @throws Magento_Core_Exception
     */
    protected function _prepareForm()
    {
        if (!$this->_formId || !$this->_tab) {
            throw new Magento_Core_Exception(
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
