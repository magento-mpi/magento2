<?php
/**
 * Adminhtml sales orders controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml;

class Order extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * @var \Magento\SalesArchive\Model\Archive
     */
    protected $_archiveModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\SalesArchive\Model\Archive $archiveModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\SalesArchive\Model\Archive $archiveModel
    ) {
        $this->_archiveModel = $archiveModel;
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline);
    }

    /**
     * Owerwrited for archive permissions validation
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        if ($this->getRequest()->getActionName() == 'view') {
            $id = $this->getRequest()->getParam('order_id');
            $archive = $this->_archiveModel;
            $ids = $archive->getIdsInArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER, $id);
            if ($ids) {
                return $this->_authorization->isAllowed('Magento_SalesArchive::orders');
            }
        }
        return parent::_isAllowed();
    }
}
