<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Search;

class Save extends \Magento\Search\Controller\Adminhtml\Search
{
    /**
     * Save search query
     *
     * @return void
     */
    public function execute()
    {
        $hasError = false;
        $data = $this->getRequest()->getPost();
        $queryId = $this->getRequest()->getPost('query_id', null);
        if ($this->getRequest()->isPost() && $data) {
            /* @var $model \Magento\Search\Model\Query */
            $model = $this->_objectManager->create('Magento\Search\Model\Query');

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
            $this->_redirect('catalog/*/edit', array('id' => $queryId));
        } else {
            $this->_redirect('catalog/*');
        }
    }
}
