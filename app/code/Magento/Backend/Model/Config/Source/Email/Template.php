<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config config system template source
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Email;

class Template extends \Magento\Object
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$collection = \Mage::registry('config_system_email_template')) {
            $collection = \Mage::getResourceModel('Magento\Core\Model\Resource\Email\Template\Collection')
                ->load();

            \Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = __('Default Template');
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = \Mage::app()->getConfig()->getNode(
            \Magento\Core\Model\Email\Template::XML_PATH_TEMPLATE_EMAIL . '/' . $nodeName . '/label'
        );
        if ($templateLabelNode) {
            $templateName = __('%1 (Default)', __((string)$templateLabelNode));
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
