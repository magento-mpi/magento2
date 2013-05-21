<?php
/**
 * Google Optimizer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Catalog_Form
{
    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Registry $registry
     * @param Varien_Data_Form $form
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Registry $registry,
        Varien_Data_Form $form,
        array $data = array()
    ) {
        $this->setForm($this->_form);
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('googleoptimizer_fields', array(
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

        $this->_form->setFieldNameSuffix('google_experiment');

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
        $entity = $this->_registry->registry('current_category');
        if (!$entity) {
            throw new RuntimeException('Entity is not found in registry.');
        }
        return $entity->getGoogleExperiment();
    }
}
