<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config config system template source
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Email_Template extends Varien_Object
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('Mage_Core_Model_Resource_Email_Template_Collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('Mage_Backend_Helper_Data')->__('Default Template');
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = Mage::app()->getConfig()->getNode(
            Mage_Core_Model_Email_Template::XML_PATH_TEMPLATE_EMAIL . '/' . $nodeName . '/label'
        );
        if ($templateLabelNode) {
            $templateName = __('%s (Default)', __((string)$templateLabelNode));
        }
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
        return $options;
    }

}
