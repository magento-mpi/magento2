<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

/**
 * URL rewrite adminhtml controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Urlrewrite extends Action
{
    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Category
     */
    protected $_category;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewrite
     */
    protected $_urlRewrite;

    /**
     * Check whether this contoller is allowed in admin permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::urlrewrite');
    }

    /**
     * Get Category from request
     *
     * @return Category
     */
    protected function _getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->_objectManager->create('Magento\Catalog\Model\Category');
            $categoryId = (int)$this->getRequest()->getParam('category', 0);

            if (!$categoryId && $this->_getUrlRewrite()->getId()) {
                $categoryId = $this->_getUrlRewrite()->getCategoryId();
            }

            if ($categoryId) {
                $this->_category->load($categoryId);
            }
        }
        return $this->_category;
    }

    /**
     * Get Product from request
     *
     * @return Product
     */
    protected function _getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $productId = (int)$this->getRequest()->getParam('product', 0);

            if (!$productId && $this->_getUrlRewrite()->getId()) {
                $productId = $this->_getUrlRewrite()->getProductId();
            }

            if ($productId) {
                $this->_product->load($productId);
            }
        }
        return $this->_product;
    }

    /**
     * Get CMS page from request
     *
     * @return \Magento\Cms\Model\Page
     */
    protected function _getCmsPage()
    {
        if (!$this->_cmsPage) {
            $this->_cmsPage = $this->_objectManager->create('Magento\Cms\Model\Page');
            $cmsPageId = (int)$this->getRequest()->getParam('cms_page', 0);

            if (!$cmsPageId && $this->_getUrlRewrite()->getId()) {
                $urlRewriteId = $this->_getUrlRewrite()->getId();
                /** @var $cmsUrlRewrite \Magento\Cms\Model\Page\Urlrewrite */
                $cmsUrlRewrite = $this->_objectManager->create('Magento\Cms\Model\Page\Urlrewrite');
                $cmsUrlRewrite->load($urlRewriteId, 'url_rewrite_id');
                $cmsPageId = $cmsUrlRewrite->getCmsPageId();
            }

            if ($cmsPageId) {
                $this->_cmsPage->load($cmsPageId);
            }
        }
        return $this->_cmsPage;
    }

    /**
     * Get URL rewrite from request
     *
     * @return \Magento\UrlRewrite\Model\UrlRewrite
     */
    protected function _getUrlRewrite()
    {
        if (!$this->_urlRewrite) {
            $this->_urlRewrite = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');

            $urlRewriteId = (int)$this->getRequest()->getParam('id', 0);
            if ($urlRewriteId) {
                $this->_urlRewrite->load((int)$this->getRequest()->getParam('id', 0));
            }
        }
        return $this->_urlRewrite;
    }
}
