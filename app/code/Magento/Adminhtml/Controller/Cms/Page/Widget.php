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
 * Controller for CMS Page Link Widget plugin
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Cms_Page_Widget extends Magento_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Cms_Page_Widget_Chooser', '', array(
            'data' => array('id' => $uniqId)
        ));
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}
