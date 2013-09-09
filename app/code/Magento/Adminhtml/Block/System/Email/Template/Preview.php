<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template preview block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_System_Email_Template_Preview extends Magento_Adminhtml_Block_Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $template Magento_Core_Model_Email_Template */
        $template = Mage::getModel('Magento_Core_Model_Email_Template',
            array('data' => array('area' => Magento_Core_Model_App_Area::AREA_FRONTEND)));
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        /* @var $filter Magento_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('Magento_Core_Model_Input_Filter_MaliciousCode');

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        Magento_Profiler::start("email_template_proccessing");
        $vars = array();

        $template->setDesignConfig(
            array(
                'area' => $this->_design->getArea(),
                'store' => Mage::getSingleton('Magento_Core_Model_StoreManagerInterface')->getDefaultStoreView()->getId()
            )
        );
        $templateProcessed = $template->getProcessedTemplate($vars, true);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Magento_Profiler::stop("email_template_proccessing");

        return $templateProcessed;
    }
}
