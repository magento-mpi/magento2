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
namespace Magento\Cms\Controller\Adminhtml\Page;

class Widget extends \Magento\Backend\Controller\Adminhtml\Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Cms\Page\Widget\Chooser', '', array(
            'data' => array('id' => $uniqId)
        ));
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}
