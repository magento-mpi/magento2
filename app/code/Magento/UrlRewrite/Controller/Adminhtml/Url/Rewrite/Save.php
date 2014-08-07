<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Model\Exception;

class Save extends \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite
{
    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
    ) {
        parent::__construct($context);
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * Call before save urlrewrite handlers
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return void
     */
    protected function _onUrlRewriteSaveBefore($model)
    {
        $this->_handleCatalogUrlRewrite($model);
        $this->_handleCmsPageUrlRewrite($model);
    }

    /**
     * Override urlrewrite data, basing on current category and product
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return void
     * @throws Exception
     */
    protected function _handleCatalogUrlRewrite($model)
    {
        $product = $this->_getInitializedProduct($model);
        $category = $this->_getInitializedCategory($model);
        if ($product || $category) {
            $isProduct = $product && $product->getId();
            $model->setEntityType($isProduct ? self::ENTITY_TYPE_PRODUCT : self::ENTITY_TYPE_CATEGORY);
            $model->setEntityId($isProduct ? $product->getId() : $category->getId());
            $model->setTargetPath($isProduct
                ? $this->productUrlPathGenerator->getCanonicalUrlPathWithCategory($product, $category)
                : $this->categoryUrlPathGenerator->getCanonicalUrlPath($category)
            );
        }
    }

    /**
     * Get product instance
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return Product|null
     */
    protected function _getInitializedProduct($model)
    {
        /** @var $product Product */
        $product = $this->_getProduct();
        if ($product->getId()) {
            $model->setProductId($product->getId());
        } else {
            $product = null;
        }

        return $product;
    }

    /**
     * Override URL rewrite data, basing on current CMS page
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    private function _handleCmsPageUrlRewrite($model)
    {
        /** @var $cmsPage \Magento\Cms\Model\Page */
        $cmsPage = $this->_getCmsPage();
        if (!$cmsPage->getId()) {
            return;
        }

        /** @var $cmsPageUrlRewrite \Magento\Cms\Model\Page\Urlrewrite */
        $cmsPageUrlRewrite = $this->_objectManager->create('Magento\Cms\Model\Page\Urlrewrite');

        $model->setTargetPath($cmsPageUrlRewrite->generateTargetPath($cmsPage));
        $model->setEntityType(self::ENTITY_TYPE_CMS_PAGE);
        $model->setEntityId($cmsPage->getId());
    }

    /**
     * Get category instance
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return Category|null
     */
    protected function _getInitializedCategory($model)
    {
        /** @var $category Category */
        $category = $this->_getCategory();
        if ($category->getId()) {
            $model->setCategoryId($category->getId());
        } else {
            $category = null;
        }
        return $category;
    }

    /**
     * Urlrewrite save action
     *
     * @return void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            /** @var $session \Magento\Backend\Model\Session */
            $session = $this->_objectManager->get('Magento\Backend\Model\Session');
            try {
                /** @var $model \Magento\UrlRewrite\Model\UrlRewrite */
                $model = $this->_getUrlRewrite();

                $requestPath = $this->getRequest()->getParam('request_path');
                $this->_objectManager->get('Magento\UrlRewrite\Helper\UrlRewrite')->validateRequestPath($requestPath);

                $model->setEntityType($this->getRequest()->getParam('entity_type', self::ENTITY_TYPE_CUSTOM))
                    ->setStoreId($this->getRequest()->getParam('store_id', 0))
                    ->setTargetPath($this->getRequest()->getParam('target_path'))
                    ->setRedirectType($this->getRequest()->getParam('redirect_type'))
                    ->setDescription($this->getRequest()->getParam('description'))
                    ->setRequestPath($requestPath)
                    ->setMetadata(serialize(['is_user_generated' => true]));

                $this->_onUrlRewriteSaveBefore($model);

                $model->save();

                $this->messageManager->addSuccess(__('The URL Rewrite has been saved.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $session->setUrlrewriteData($data);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while saving URL Rewrite.'));
                $session->setUrlrewriteData($data);
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
