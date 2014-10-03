<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Term;

class MassDelete extends \Magento\Search\Controller\Adminhtml\Term
{
    /**
     * @return void
     */
    public function execute()
    {
        $searchIds = $this->getRequest()->getParam('search');
        if (!is_array($searchIds)) {
            $this->messageManager->addError(__('Please select searches.'));
        } else {
            try {
                foreach ($searchIds as $searchId) {
                    $model = $this->_objectManager->create('Magento\Search\Model\Query')->load($searchId);
                    $model->delete();
                }
                $this->messageManager->addSuccess(__('Total of %1 record(s) were deleted', count($searchIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('catalog/*/index');
    }
}
