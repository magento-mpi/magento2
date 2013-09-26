<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Sales;

class Order extends \Magento\Adminhtml\Controller\Sales\Order
{
    /**
     * @var \Magento\SalesArchive\Model\Archive
     */
    protected $_archiveModel;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\SalesArchive\Model\Archive $archiveModel
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\SalesArchive\Model\Archive $archiveModel
    ) {
        $this->_archiveModel = $archiveModel;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Owerwrited for archive permissions validation
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
