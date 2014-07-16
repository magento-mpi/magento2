<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Controller\Adminhtml;

/**
 * Adminhtml Survey Action
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Survey extends \Magento\Backend\App\Action
{

    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(null);
    }
}
