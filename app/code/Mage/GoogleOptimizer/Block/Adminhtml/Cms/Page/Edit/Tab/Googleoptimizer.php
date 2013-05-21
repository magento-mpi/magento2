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
    extends Mage_Backend_Block_Widget_Form
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helperData;

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
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->setForm($form);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $fieldset = $this->getForm()->addFieldset('googleoptimizer_fields', array(
            'legend' => $this->__('Google Analytics Content Experiments Code')
        ));

        $experimentCode = array();
        $experimentId = '';

        if (null != ($experiment = $this->_getGoogleExperiment())) {
            $experimentCode = $experiment->getExperimentScript();
            $experimentId = $experiment->getCodeId();
        }

        $fieldset->addField('experiment_script', 'textarea', array(
            'name' => 'experiment_script',
            'label' => $this->__('Experiment Code'),
            'value' => $experimentCode,
            'class' => 'textarea googleoptimizer',
            'required' => false,
            'note' => $this->__('Note: Experiment code should be added to the original page only.'),
        ));

        $fieldset->addField('code_id', 'hidden', array(
            'name' => 'code_id',
            'value' => $experimentId,
            'required' => false,
        ));

        $this->getForm()->setFieldNameSuffix('google_experiment');

        return parent::_prepareForm();
    }

    /**
     * Get google experiment code model
     *
     * @return Mage_GoogleOptimizer_Model_Code
     * @throws RuntimeException
     */
    protected function _getGoogleExperiment()
    {
        $entity = $this->_registry->registry('cms_page');
        if (!$entity) {
            throw new RuntimeException('Entity is not found in registry.');
        }
        return $entity->getGoogleExperiment();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Page View Optimization');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Page View Optimization');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_helperData->isGoogleExperimentActive();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
