<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Controller for CMS Page Link Widget plugin
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Cms_Page_Widget extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Cms_Page_Widget_Chooser', '', array(
            'data' => array('id' => $uniqId)
        ));
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}
