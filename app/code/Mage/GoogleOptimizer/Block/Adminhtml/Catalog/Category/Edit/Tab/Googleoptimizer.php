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
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Catalog_Model_Category $category,
        Mage_GoogleOptimizer_Helper_Data $helperData,
        Mage_Core_Model_Registry $registry,
        Varien_Data_Form $form,
        array $data = array()
    ) {
        $this->_category = $category;
        $this->_helperData = $helperData;
        $this->_form = $form;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getGoogleOptimizer()
    {
        return $this->getCategory()->getGoogleOptimizerScripts();
    }

    public function _prepareLayout()
    {
        $fieldset = $this->_form->addFieldset('googleoptimizer_fields',
            array('legend' => $this->__('Google Analytics Content Experiments Code'))
        );
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getCategory();

        $disabledScriptsFields = false;
        $experimentCode = array();
        $experimentId = '';

        if ($category->getGoogleExperiment()) {
            $experimentCode = $category->getGoogleExperiment()->getExperimentScript();
            $experimentId = $category->getGoogleExperiment()->getCodeId();
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
}
