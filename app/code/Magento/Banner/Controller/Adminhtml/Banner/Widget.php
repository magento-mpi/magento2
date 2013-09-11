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

class Widget extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->getLayout()->createBlock(
            '\Magento\Banner\Block\Adminhtml\Widget\Chooser', '', array('data' => array('id' => $uniqId))
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
