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
     * @var Magento_Core_Model_Input_Filter_MaliciousCode
     */
    protected $_maliciousCode;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_emailFactory;

    /**
     * @param Magento_Core_Model_Input_Filter_MaliciousCode $maliciousCode
     * @param Magento_Core_Model_Email_TemplateFactory $emailFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Input_Filter_MaliciousCode $maliciousCode,
        Magento_Core_Model_Email_TemplateFactory $emailFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_maliciousCode = $maliciousCode;
        $this->_emailFactory = $emailFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $template Magento_Core_Model_Email_Template */
        $template = $this->_emailFactory->create(
            array('data' => array('area' => Magento_Core_Model_App_Area::AREA_FRONTEND))
        );
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        $template->setTemplateText(
            $this->_maliciousCode->filter($template->getTemplateText())
        );

        Magento_Profiler::start("email_template_proccessing");
        $vars = array();

        $template->setDesignConfig(
            array(
                'area' => $this->_design->getArea(),
                'store' => $this->_storeManager->getDefaultStoreView()->getId()
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
