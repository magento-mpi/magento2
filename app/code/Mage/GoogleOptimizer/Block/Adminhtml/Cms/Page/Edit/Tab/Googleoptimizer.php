<?php
/**
 * Google Optimizer Cms Page Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helperData;

    /**
     * @var Varien_Data_Form
     */
    protected $_form;

    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Helper_Data $helperData
     * @param Mage_Core_Model_Registry $registry
     * @param Varien_Data_Form $form
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_GoogleOptimizer_Helper_Data $helperData,
        Mage_Core_Model_Registry $registry,
        Varien_Data_Form $form,
        array $data = array()
    ) {
        $this->_helperData = $helperData;
        $this->_form = $form;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('googleoptimizer_fields',
            array('legend' => $this->__('Google Analytics Content Experiments Code'))
        );

        $disabledScriptsFields = false;
        $experimentCode = array();
        $experimentId = '';

        if ($this->getPage()->getGoogleExperiment()) {
            $experimentCode = $this->getPage()->getGoogleExperiment()->getExperimentScript();
            $experimentId = $this->getPage()->getGoogleExperiment()->getCodeId();
        }

        $fieldset->addField('experiment_script', 'textarea',
            array(
                'name'  => 'experiment_script',
                'label' => $this->__('Experiment Code'),
                'value' => $experimentCode,
                'class' => 'textarea googleoptimizer',
                'required' => false,
                'note' => $this->__('Note: Experiment code should be added to the original page only.'),
            )
        );

        $fieldset->addField('code_id', 'hidden',
            array(
                'name'  => 'code_id',
                'value' => $experimentId,
                'required' => false,
            )
        );

        $this->_form->setFieldNameSuffix('google_experiment');
        $this->setForm($this->_form);

        return parent::_prepareForm();
    }

    public function getPage()
    {
        return $this->_registry->registry('cms_page');
    }

    public function getGoogleOptimizer()
    {
        return $this->getPage()->getGoogleOptimizerScripts();
    }

    public function getTabLabel()
    {
        return $this->_helperData->__('Page View Optimization');
    }

    public function getTabTitle()
    {
        return $this->_helperData->__('Page View Optimization');
    }

    public function canShowTab()
    {
        if ($this->_helperData->isGoogleExperimentActive()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        return false;
    }
}
