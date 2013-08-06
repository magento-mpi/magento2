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

        return $this->_getTemplate()->getCollection()
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
        $templateLabelNode = $this->_getConfig()->getNode(
            Saas_PrintedTemplate_Model_Template::XML_PATH_TEMPLATE_PRINTED . '/' . $nodeName . '/label'
        );

        $defaultTemplateName = ($templateLabelNode)
            ? $this->_getHelper()->
                __('%1 (Default Template from Locale)', $this->_getHelper()->__((string)$templateLabelNode))
            : $this->_getHelper()->__('Default Template from Locale');

        return array('value' => $nodeName, 'label' => $defaultTemplateName);
    }


    protected function _getConfig()
    {
        return Mage::app()->getConfig();
    }

    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }

    protected function _getTemplate()
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Template');
    }

}
