<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Page controller
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Controller_Page extends Magento_Core_Controller_Front_Action
{
    /**
     * View CMS page action
     *
     */
    public function viewAction()
    {
        $pageId = $this->getRequest()
            ->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!Mage::helper('Magento_Cms_Helper_Page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
    }
}
