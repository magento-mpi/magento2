<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template preview block
 *
 * @category   Magento
 * @package    Magento_Email
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Email\Block\Adminhtml\Template;

class Preview extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Filter\Input\MaliciousCode
     */
    protected $_maliciousCode;

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $_emailFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Filter\Input\MaliciousCode $maliciousCode
     * @param \Magento\Email\Model\TemplateFactory $emailFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Filter\Input\MaliciousCode $maliciousCode,
        \Magento\Email\Model\TemplateFactory $emailFactory,
        array $data = array()
    ) {
        $this->_maliciousCode = $maliciousCode;
        $this->_emailFactory = $emailFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $template \Magento\Email\Model\Template */
        $template = $this->_emailFactory->create(
            array('data' => array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND))
        );
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $template->setTemplateText($this->_maliciousCode->filter($template->getTemplateText()));

        \Magento\Profiler::start("email_template_proccessing");
        $vars = array();

        $template->setDesignConfig(
            array('area' => $this->_design->getArea(), 'store' => $this->_storeManager->getAnyStoreView()->getId())
        );
        $templateProcessed = $template->getProcessedTemplate($vars, true);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        \Magento\Profiler::stop("email_template_proccessing");

        return $templateProcessed;
    }
}
