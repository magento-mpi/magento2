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
 * Adminhtml config printed template abstract source
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
abstract class Saas_PrintedTemplate_Model_Source_Template_Abstract extends Varien_Object
{
    /**
     * Returns one of Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_* constants value
     *
     * @return string
     */
    abstract protected function _getEntityType();

    /**
     * Generate list of printed invoice templates
     * with default one
     *
     * @return array Array of array(value => ... , label => ...)
     */
    public function toOptionArray()
    {
        $options = $this->_getTypeCollection($this->_getEntityType())
            ->toOptionArray();
        array_unshift($options, $this->_getDefaultOption());

        return $options;
    }

    /**
     * Get printed templates collection filtered by type
     *
     * @param string $templateType
     * @return Saas_PrintedTemplate_Model_Resource_Template_Collection
     */
    protected function _getTypeCollection($templateType)
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Template')
            ->getCollection()
            ->addFieldToFilter('entity_type', array('eq', $templateType));
    }

    /**
     * Get default printed template option (locale template from file system)
     *
     * @return array
     */
    protected function _getDefaultOption()
    {
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = Mage::app()->getConfig()->getNode(
            Saas_PrintedTemplate_Model_Template::XML_PATH_TEMPLATE_PRINTED . '/' . $nodeName . '/label'
        );

        $defaultTemplateName = ($templateLabelNode)
            ? Mage::helper('Saas_PrintedTemplate_Helper_Data')
                ->__('%s (Default Template from Locale)', Mage::helper('Saas_PrintedTemplate_Helper_Data')
                    ->__((string)$templateLabelNode)
                ) : Mage::helper('Saas_PrintedTemplate_Helper_Data')->__('Default Template from Locale');

        return array('value' => $nodeName, 'label' => $defaultTemplateName);
    }
}
