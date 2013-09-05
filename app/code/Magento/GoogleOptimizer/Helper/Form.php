<?php
/**
 * Google Optimizer Form Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Helper_Form extends Magento_Core_Helper_Abstract
{
    /**
     * Prepare form
     *
     * @param \Magento\Data\Form $form
     * @param Magento_GoogleOptimizer_Model_Code|null $experimentCodeModel
     */
    public function addGoogleoptimizerFields(
        \Magento\Data\Form $form,
        Magento_GoogleOptimizer_Model_Code $experimentCodeModel = null
    ) {
        $fieldset = $form->addFieldset('googleoptimizer_fields', array(
            'legend' => __('Google Analytics Content Experiments Code'),
        ));

        $fieldset->addField('experiment_script', 'textarea', array(
            'name' => 'experiment_script',
            'label' => __('Experiment Code'),
            'value' => $experimentCodeModel ? $experimentCodeModel->getExperimentScript() : array(),
            'class' => 'textarea googleoptimizer',
            'required' => false,
            'note' => __('Note: Experiment code should be added to the original page only.'),
        ));

        $fieldset->addField('code_id', 'hidden', array(
            'name' => 'code_id',
            'value' => $experimentCodeModel ? $experimentCodeModel->getCodeId() : '',
            'required' => false,
        ));

        $form->setFieldNameSuffix('google_experiment');
    }
}
