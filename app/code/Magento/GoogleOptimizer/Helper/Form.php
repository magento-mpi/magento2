<?php
/**
 * Google Optimizer Form Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Helper;

use Magento\GoogleOptimizer\Model\Code as ModelCode;
use Magento\Data\Form as DataForm;

class Form extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Prepare form
     *
     * @param DataForm $form
     * @param Code|null $experimentCodeModel
     * @return void
     */
    public function addGoogleoptimizerFields(
        DataForm $form,
        ModelCode $experimentCodeModel = null
    ) {
        $fieldset = $form->addFieldset('googleoptimizer_fields', array(
            'legend' => __('Google Analytics Content Experiments Code'),
        ));

        $fieldset->addField('experiment_script', 'textarea', array(
            'name' => 'experiment_script',
            'label' => __('Experiment Code'),
            'value' => $experimentCodeModel ? $experimentCodeModel->getExperimentScript() : '',
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
