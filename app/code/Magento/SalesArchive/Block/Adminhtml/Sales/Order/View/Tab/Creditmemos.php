<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab;

/**
 * Creditmemos tab
 */
class Creditmemos
     extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Creditmemos
{
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Creditmemo\Collection';
    }
}
