<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Controller\Adminhtml;

class Export extends \Magento\Backend\App\Action
{
    /**
     * Check access (in the ACL) for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ImportExport::export');
    }
}
