<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Controller_Adminhtml_Sales_Order extends Magento_Adminhtml_Controller_Sales_Order
{
    /**
     * Owerwrited for archive permissions validation
     */
    protected function _isAllowed()
    {
        if ($this->getRequest()->getActionName() == 'view') {
            $id = $this->getRequest()->getParam('order_id');
            $archive = Mage::getModel('Enterprise_SalesArchive_Model_Archive');
            $ids = $archive->getIdsInArchive(Enterprise_SalesArchive_Model_Archive::ORDER, $id);
            if ($ids) {
                return $this->_authorization->isAllowed('Enterprise_SalesArchive::orders');
            }
        }
        return parent::_isAllowed();
    }
}
