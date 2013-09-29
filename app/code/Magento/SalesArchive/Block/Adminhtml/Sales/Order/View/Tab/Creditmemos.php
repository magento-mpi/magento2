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
 * Creditmemos tab
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab;

class Creditmemos
     extends \Magento\Adminhtml\Block\Sales\Order\View\Tab\Creditmemos
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
