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
class Magento_SalesArchive_Controller_Adminhtml_Sales_Order extends Magento_Adminhtml_Controller_Sales_Order
{
    /**
     * @var Magento_SalesArchive_Model_Archive
     */
    protected $_archiveModel;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_SalesArchive_Model_Archive $archiveModel
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_SalesArchive_Model_Archive $archiveModel
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
            $ids = $archive->getIdsInArchive(Magento_SalesArchive_Model_ArchivalList::ORDER, $id);
            if ($ids) {
                return $this->_authorization->isAllowed('Magento_SalesArchive::orders');
            }
        }
        return parent::_isAllowed();
    }
}
