<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class CategoriesJson extends \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite
{
    /**
     * Ajax categories tree loader action
     *
     * @return void
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(
            $this->_objectManager->get(
                'Magento\UrlRewrite\Block\Catalog\Category\Tree'
            )->getTreeArray(
                $categoryId,
                true,
                1
            )
        );
    }
}
