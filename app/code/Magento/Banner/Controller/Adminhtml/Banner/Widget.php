<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Widget extends \Magento\Backend\App\Action
{
    /**
     * Chooser Source action
     *
     * @return void
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->_view->getLayout()->createBlock(
            'Magento\Banner\Block\Adminhtml\Widget\Chooser', '', array('data' => array('id' => $uniqId))
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
