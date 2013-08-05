<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom variables for printed templates
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Source_Variables
{
    /**
     * Prepares option array of printed template variables
     *
     * @param int $templateType
     * @return array
     */
    public function toOptionArray($templateType = null)
    {
        $optionArray = array();
        $variables = $this->_getConfigModelSingeleton()->getVariablesArray($templateType);

        foreach ($variables as $entity => $options) {
            if (!isset($options['fields'])) {
                continue;
            }
            $optionArrayVariables = array();
            foreach ($options['fields'] as $fieldName => $variable) {
                $optionArrayVariables[] = array(
                    'value' => '{{var ' . $entity . '.' . $fieldName . '}}',
                    'label' => __($variable['label'])
                );
            }
            $label = isset($options['label']) ? $options['label'] : $entity;
            $optionArray[] = array(
                'label' => __($label),
                'value' => $optionArrayVariables
            );
        }

        return $optionArray;
    }

    /**
     * Returns Config model
     *
     * @return  Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfigModelSingeleton()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Config');
    }

    /**
     * Returns Data helper
     *
     * @return  Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }
}
