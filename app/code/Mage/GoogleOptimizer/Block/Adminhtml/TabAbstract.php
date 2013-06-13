<?php
/**
 * Abstract Google Experiment Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Mage_GoogleOptimizer_Block_Adminhtml_TabAbstract
    extends Mage_Backend_Block_Widget_Form implements Mage_Backend_Block_Widget_Tab_Interface
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
     * @var Mage_GoogleOptimizer_Helper_Code
     */
    protected $_codeHelper;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Helper_Data $helperData
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_GoogleOptimizer_Helper_Code $codeHelper
     * @param Varien_Data_Form $form
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_GoogleOptimizer_Helper_Data $helperData,
        Mage_Core_Model_Registry $registry,
        Mage_GoogleOptimizer_Helper_Code $codeHelper,
        Varien_Data_Form $form,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_helperData = $helperData;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
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
     * @return Mage_GoogleOptimizer_Model_Code|null
     */
    protected function _getGoogleExperiment()
    {
        $entity = $this->_getEntity();
        if ($entity->getId()) {
            return $this->_codeHelper->getCodeObjectByEntity($entity);
        }
        return null;
    }

    /**
     * Get Entity model
     *
     * @return Mage_Catalog_Model_Abstract
     */
    protected abstract function _getEntity();

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
