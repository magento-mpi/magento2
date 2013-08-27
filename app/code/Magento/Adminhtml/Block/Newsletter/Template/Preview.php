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
 * Adminhtml newsletter template preview block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Newsletter_Template_Preview extends Magento_Adminhtml_Block_Widget
{

    protected function _toHtml()
    {
        /* @var $template Magento_Newsletter_Model_Template */
        $template = Mage::getModel('Magento_Newsletter_Model_Template');

        if($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');
        if(!$storeId) {
            $storeId = Mage::app()->getDefaultStoreView()->getId();
        }

        Magento_Profiler::start("newsletter_template_proccessing");
        $vars = array();

        $vars['subscriber'] = Mage::getModel('Magento_Newsletter_Model_Subscriber');
        if($this->getRequest()->getParam('subscriber')) {
            $vars['subscriber']->load($this->getRequest()->getParam('subscriber'));
        }

        $template->emulateDesign($storeId);
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        $template->revertDesign();

        if($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Magento_Profiler::stop("newsletter_template_proccessing");

        return $templateProcessed;
    }

}
