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
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;

class Save extends \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite
{
    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /** @var \Magento\CmsUrlRewrite\Model\CmsPageUrlPathGenerator */
    protected $cmsPageUrlPathGenerator;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param \Magento\CmsUrlRewrite\Model\CmsPageUrlPathGenerator $cmsPageUrlPathGenerator
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        \Magento\CmsUrlRewrite\Model\CmsPageUrlPathGenerator $cmsPageUrlPathGenerator,
        UrlFinderInterface $urlFinder
    ) {
        parent::__construct($context);
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->cmsPageUrlPathGenerator = $cmsPageUrlPathGenerator;
        $this->urlFinder = $urlFinder;
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
        $productId = $this->_getProduct()->getId();
        $categoryId = $this->_getCategory()->getId();
        if ($productId || $categoryId) {
            if ($model->isObjectNew()) {
                $model->setEntityType($productId ? self::ENTITY_TYPE_PRODUCT : self::ENTITY_TYPE_CATEGORY)
                    ->setEntityId($productId ? : $categoryId);
                if ($productId && $categoryId) {
                    $model->setMetadata(serialize(['category_id' => $categoryId]));
                }
            }
            $targetPath = $this->getCanonicalTargetPath();
            if ($model->getRedirectType()) {
                $data = [
                    UrlRewrite::ENTITY_ID => $model->getEntityId(),
                    UrlRewrite::TARGET_PATH => $targetPath,
                    UrlRewrite::ENTITY_TYPE => $model->getEntityType(),
                    UrlRewrite::STORE_ID => $model->getStoreId(),
                ];
                $rewrite = $this->urlFinder->findOneByData($data);
                if (!$rewrite) {
                    $message = $productId
                        ? __('Chosen product does not associated with the chosen store or category.')
                        : __('Chosen category does not associated with the chosen store.');
                    throw new Exception($message);
                }
                $targetPath = $rewrite->getRequestPath();
            }
            $model->setTargetPath($targetPath);
        }
    }

    /**
     * @return string
     */
    protected function getCanonicalTargetPath()
    {
        $product = $this->_getProduct()->getId() ? $this->_getProduct() : null;
        $category = $this->_getCategory()->getId() ? $this->_getCategory() : null;
        return $product
            ? $this->productUrlPathGenerator->getCanonicalUrlPath($product, $category)
            : $this->categoryUrlPathGenerator->getCanonicalUrlPath($category);
    }

    /**
     * Override URL rewrite data, basing on current CMS page
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $model
     * @return void
     */
    private function _handleCmsPageUrlRewrite($model)
    {
        $cmsPage = $this->_getCmsPage();
        if ($cmsPage->getId()) {
            if ($model->isObjectNew()) {
                $model->setEntityType(self::ENTITY_TYPE_CMS_PAGE)->setEntityId($cmsPage->getId());
            }
            $model->setTargetPath(
                $model->getRedirectType()
                    ? $this->cmsPageUrlPathGenerator->getUrlPath($cmsPage)
                    : $this->cmsPageUrlPathGenerator->getCanonicalUrlPath($cmsPage)
            );
        }
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var $session \Magento\Backend\Model\Session */
            $session = $this->_objectManager->get('Magento\Backend\Model\Session');
            try {
                $model = $this->_getUrlRewrite();

                $requestPath = $this->getRequest()->getParam('request_path');
                $this->_objectManager->get('Magento\UrlRewrite\Helper\UrlRewrite')->validateRequestPath($requestPath);

                $model->setEntityType($this->getRequest()->getParam('entity_type', self::ENTITY_TYPE_CUSTOM))
                    ->setRequestPath($requestPath)
                    ->setTargetPath($this->getRequest()->getParam('target_path', $model->getTargetPath()))
                    ->setRedirectType($this->getRequest()->getParam('redirect_type'))
                    ->setStoreId($this->getRequest()->getParam('store_id', 0))
                    ->setDescription($this->getRequest()->getParam('description'));

                $this->_handleCatalogUrlRewrite($model);
                $this->_handleCmsPageUrlRewrite($model);
                $model->save();

                $this->messageManager->addSuccess(__('The URL Rewrite has been saved.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $session->setUrlRewriteData($data);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while saving URL Rewrite.'));
                $session->setUrlRewriteData($data);
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
