<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dashboard admin controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\Adminhtml;

class Dashboard extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::dashboard');
    }
}
