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
    /**
     * @var Magento_Newsletter_Model_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento_Newsletter_Model_SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param Magento_Newsletter_Model_TemplateFactory $templateFactory
     * @param Magento_Newsletter_Model_SubscriberFactory $subscriberFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Newsletter_Model_TemplateFactory $templateFactory,
        Magento_Newsletter_Model_SubscriberFactory $subscriberFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _toHtml()
    {
        /* @var $template Magento_Newsletter_Model_Template */
        $template = $this->_templateFactory->create();

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');
        if (!$storeId) {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        Magento_Profiler::start("newsletter_template_proccessing");
        $vars = array();

        $vars['subscriber'] = $this->_subscriberFactory->create();
        if ($this->getRequest()->getParam('subscriber')) {
            $vars['subscriber']->load($this->getRequest()->getParam('subscriber'));
        }

        $template->emulateDesign($storeId);
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        $template->revertDesign();

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Magento_Profiler::stop("newsletter_template_proccessing");

        return $templateProcessed;
    }
}
