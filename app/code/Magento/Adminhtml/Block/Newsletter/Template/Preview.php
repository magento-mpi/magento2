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
namespace Magento\Adminhtml\Block\Newsletter\Template;

class Preview extends \Magento\Adminhtml\Block\Widget
{

    protected function _toHtml()
    {
        /* @var $template \Magento\Newsletter\Model\Template */
        $template = \Mage::getModel('Magento\Newsletter\Model\Template');

        if($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');
        if(!$storeId) {
            $storeId = \Mage::app()->getDefaultStoreView()->getId();
        }

        \Magento\Profiler::start("newsletter_template_proccessing");
        $vars = array();

        $vars['subscriber'] = \Mage::getModel('Magento\Newsletter\Model\Subscriber');
        if($this->getRequest()->getParam('subscriber')) {
            $vars['subscriber']->load($this->getRequest()->getParam('subscriber'));
        }

        $template->emulateDesign($storeId);
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        $template->revertDesign();

        if($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        \Magento\Profiler::stop("newsletter_template_proccessing");

        return $templateProcessed;
    }

}
