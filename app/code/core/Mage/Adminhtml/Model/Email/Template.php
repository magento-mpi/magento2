<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml email template model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Xml path to email template nodes
     *
     */
    const XML_PATH_TEMPLATE_EMAIL = '//sections/*/groups/*/fields/*[source_model="Mage_Adminhtml_Model_System_Config_Source_Email_Template"]';

    /**
     * Collect all system config pathes where current template is used as default
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedAsDefault()
    {
        $templateCode = $this->getOrigTemplateCode();
        if (!$templateCode) {
            return array();
        }
        $paths = array();

        $configSections = Mage::getSingleton('Mage_Backend_Model_System_Config_Structure')->getSections();

        // find nodes which are using $templateCode value
        $defaultCfgNodes = Mage::getConfig()->getXpath('default/*/*[*="' . $templateCode . '"]');
        if (!is_array($defaultCfgNodes)) {
            return array();
        }

        foreach ($defaultCfgNodes as $node) {
            // create email template path in system.xml
            $sectionName = $node->getParent()->getName();
            $groupName = $node->getName();
            $fieldName = substr($templateCode, strlen($sectionName . '_' . $groupName . '_'));
            $paths[] = array('path' => implode('/', array($sectionName, $groupName, $fieldName)));
        }
        return $paths;
    }

    /**
     * Collect all system config pathes where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        $templateId = $this->getId();
        if (!$templateId) {
            return array();
        }
        $paths = array();

        $configSections = Mage::getSingleton('Mage_Backend_Model_System_Config_Structure')->getSections();

        // look for node entries in all system.xml that use source_model=adminhtml/system_config_source_email_template
        // they are will be templates, what we try find
        $sysCfgNodes    = $configSections->xpath(self::XML_PATH_TEMPLATE_EMAIL);
        if (!is_array($sysCfgNodes)) {
            return array();
        }

        foreach ($sysCfgNodes as $fieldNode) {

            $groupNode = $fieldNode->getParent()->getParent();
            $sectionNode = $groupNode->getParent()->getParent();

            // create email template path in system.xml
            $sectionName = $sectionNode->getName();
            $groupName = $groupNode->getName();
            $fieldName = $fieldNode->getName();

            $paths[] = implode('/', array($sectionName, $groupName, $fieldName));
        }

        $configData = $this->_getResource()->getSystemConfigByPathsAndTemplateId($paths, $templateId);
        if (!$configData) {
            return array();
        }

        return $configData;
    }
}
