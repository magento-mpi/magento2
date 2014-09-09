<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class ChangeStatus extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * @var \Magento\GiftWrapping\Model\Resource\Wrapping
     */
    protected $wrappingResource;

    /**
     * @param $wrappingResource $wrappingModelResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\GiftWrapping\Model\Resource\Wrapping $wrappingModelResource
    ) {
        $this->wrappingModelResource = $wrappingModelResource;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Change gift wrapping(s) status action
     *
     * @return void
     */
    public function execute()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        $status = (int)(bool)$this->getRequest()->getParam('status');
        try {
            $this->wrappingModelResource->updateStatus($status, $wrappingIds);
            $this->messageManager->addSuccess(__('You updated a total of %1 records.', count($wrappingIds)));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while updating the wrapping(s) status.'));
        }

        $this->_redirect('adminhtml/*/index');
    }
}
