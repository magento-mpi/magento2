<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Search;

class Save extends \Magento\Catalog\Controller\Adminhtml\Search
{
    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Save search query
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $hasError = false;
        $data = $this->getRequest()->getPost();
        $queryId = $this->getRequest()->getPost('query_id', null);
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $model \Magento\CatalogSearch\Model\Query */
            $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query');

            // validate query
            $queryText = $this->getRequest()->getPost('query_text', false);
            $storeId = $this->getRequest()->getPost('store_id', false);

            try {
                if ($queryText) {
                    $model->setStoreId($storeId);
                    $model->loadByQueryText($queryText);
                    if ($model->getId() && $model->getId() != $queryId) {
                        throw new \Magento\Framework\Model\Exception(
                            __('You already have an identical search term query.')
                        );
                    } elseif (!$model->getId() && $queryId) {
                        $model->load($queryId);
                    }
                } else if ($queryId) {
                    $model->load($queryId);
                }

                $model->addData($data);
                $model->setIsProcessed(0);
                $model->save();
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $hasError = true;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the search query.'));
                $hasError = true;
            }
        }

        if ($hasError) {
            $this->_getSession()->setPageData($data);
            return $redirectResult->setPath('catalog/*/edit', ['id' => $queryId]);
        } else {
            return $redirectResult->setPath('catalog/*');
        }
    }
}
