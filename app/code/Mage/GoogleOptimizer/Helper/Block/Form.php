<?php
/**
 * Google Optimizer Form Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Helper_Block_Form
{
    /**
     * Prepare form
     *
     * @param Mage_Backend_Block_Widget_Form $formWidget
     * @param Mage_GoogleOptimizer_Model_Code|null $experimentCodeModel
     */
    public function prepareForm(Mage_Backend_Block_Widget_Form $formWidget, $experimentCodeModel)
    {
        $fieldset = $formWidget->getForm()->addFieldset('googleoptimizer_fields', array(
            'legend' => $formWidget->__('Google Analytics Content Experiments Code')
        ));

        $experimentCode = array();
        $experimentId = '';

        if (null != $experimentCodeModel) {
            $experimentCode = $experimentCodeModel->getExperimentScript();
            $experimentId = $experimentCodeModel->getCodeId();
        }

        $fieldset->addField('experiment_script', 'textarea', array(
            'name' => 'experiment_script',
            'label' => $formWidget->__('Experiment Code'),
            'value' => $experimentCode,
            'class' => 'textarea googleoptimizer',
            'required' => false,
            'note' => $formWidget->__('Note: Experiment code should be added to the original page only.'),
        ));

        $fieldset->addField('code_id', 'hidden', array(
            'name' => 'code_id',
            'value' => $experimentId,
            'required' => false,
        ));

        $formWidget->getForm()->setFieldNameSuffix('google_experiment');
    }
}
