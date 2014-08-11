<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Adminhtml;

/**
 * Adminhtml Survey Action
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
