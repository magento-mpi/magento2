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
namespace Magento\Adminhtml\Block\System\Email\Template;

class Preview extends \Magento\Adminhtml\Block\Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $template \Magento\Core\Model\Email\Template */
        $template = \Mage::getModel('Magento\Core\Model\Email\Template',
            array('data' => array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND)));
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }

        /* @var $filter \Magento\Core\Model\Input\Filter\MaliciousCode */
        $filter = \Mage::getSingleton('Magento\Core\Model\Input\Filter\MaliciousCode');

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        \Magento\Profiler::start("email_template_proccessing");
        $vars = array();

        $template->setDesignConfig(
            array(
                'area' => $this->_design->getArea(),
                'store' => \Mage::getSingleton('Magento\Core\Model\StoreManagerInterface')->getDefaultStoreView()->getId()
            )
        );
        $templateProcessed = $template->getProcessedTemplate($vars, true);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        \Magento\Profiler::stop("email_template_proccessing");

        return $templateProcessed;
    }
}
