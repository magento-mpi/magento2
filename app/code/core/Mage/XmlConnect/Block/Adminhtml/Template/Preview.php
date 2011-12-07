<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect Adminhtml AirMail template preview block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Template_Preview extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve processed template
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ((int)$this->getRequest()->getParam('queue_preview')) {
            $id = $this->getRequest()->getParam('queue_preview');
            /** @var $template Mage_XmlConnect_Model_Queue */
            $template = Mage::getModel('Mage_XmlConnect_Model_Queue');
        } else {
            $id = (int)$this->getRequest()->getParam('id');
            /** @var $template Mage_XmlConnect_Model_Template */
            $template = Mage::getModel('Mage_XmlConnect_Model_Template');
        }

        if ($id) {
            $template->load($id);
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');

        if (!$storeId) {
            $storeId = Mage::app()->getDefaultStoreView()->getId();
        }

        $template->emulateDesign($storeId);
        $templateProcessed = $template->getProcessedTemplate(array(), true);
        $template->revertDesign();

        return $templateProcessed;
    }
}
