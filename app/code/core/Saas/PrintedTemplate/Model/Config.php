<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Retrives variables for printed templates from config
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Config
{
    /**
     * Path to printed template config
     */
    const XML_PATH_PRINTEDTEMPLATE = 'global/saas_printedtemplate';

    /**
     * Returns array of variables from config
     *
     * @param string $templateType
     * @return array
     */
    public function getVariablesArray($templateType = null)
    {
        $variables = $this->getConfigSectionArray('variables');
        // filter variables by entity type
        if ($variables && $templateType) {
            foreach ($variables as $entity => &$options) {
                if (isset($options['template_type'])
                    && (!is_array($options['template_type']) || !isset($options['template_type'][$templateType]))) {
                    unset($variables[$entity]);
                    continue;
                }
                if (isset($options['fields'])) {
                    foreach ($options['fields'] as $fieldId => $fieldOptions) {
                        if (isset($fieldOptions['template_type'])
                            && !isset($fieldOptions['template_type'][$templateType])) {
                            unset($options['fields'][$fieldId]);
                        }
                    }
                }
            }
        }
        return $variables;
    }

    /**
     * Returns array of item properties from specified entity type
     *
     * @param string $type
     * @return array
     */
    public function getItemPropertiesArray($type)
    {
        return $this->getConfigSectionArray('variables/item_' . $type . '/fields');
    }

    /**
     * Returns array of names of avaliable fonts
     *
     * @return array
     */
    public function getFontsArray()
    {
        return $this->getConfigSectionArray('fonts');
    }

    /**
     * Returns array of avaliable font sizes
     *
     * @param string $type
     * @return array
     */
    public function getFontSizesArray()
    {
        return explode(',', $this->getConfigSectionArray('font_sizes'));
    }

    /**
     * Get data from some section from printed template config
     *
     * @param string $section
     * @return array
     */
    public function getConfigSectionArray($section)
    {
        return Mage::getConfig()->getNode(self::XML_PATH_PRINTEDTEMPLATE . '/' . $section)->asArray();
    }

    /**
     * Returns template ID by type from system -> configuration option
     *
     * @param string $templateType Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_*
     * @param int $storeId
     */
    public function getTemplateIdByType($templateType, $storeId = null)
    {
        return Mage::getStoreConfig("sales_pdf/$templateType/printed_template", $storeId);
    }
}
