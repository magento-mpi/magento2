<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter template preview block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml\Queue;

class Preview extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Newsletter\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Newsletter\Model\QueueFactory
     */
    protected $_queueFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Newsletter\Model\TemplateFactory $templateFactory
     * @param \Magento\Newsletter\Model\QueueFactory $queueFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Newsletter\Model\TemplateFactory $templateFactory,
        \Magento\Newsletter\Model\QueueFactory $queueFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_queueFactory = $queueFactory;
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        /* @var $template \Magento\Newsletter\Model\Template */
        $template = $this->_templateFactory->create();

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $queue = $this->_queueFactory->create()->load($id);
            $template->setTemplateType($queue->getNewsletterType());
            $template->setTemplateText($queue->getNewsletterText());
            $template->setTemplateStyles($queue->getNewsletterStyles());
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');
        if (!$storeId) {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        \Magento\Profiler::start("newsletter_queue_proccessing");
        $vars = array();

        $vars['subscriber'] = $this->_subscriberFactory->create();

        $template->emulateDesign($storeId);
        $templateProcessed = $this->_appState->emulateAreaCode(
            \Magento\Newsletter\Model\Template::DEFAULT_DESIGN_AREA,
            array($template, 'getProcessedTemplate'),
            array($vars, true)
        );
        $template->revertDesign();

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        \Magento\Profiler::stop("newsletter_queue_proccessing");

        return $templateProcessed;
    }
}
