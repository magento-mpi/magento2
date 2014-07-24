<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Controller\Adminhtml;

/**
 * Import controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Import extends \Magento\Backend\App\Action
{
    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ImportExport::import');
    }
}
