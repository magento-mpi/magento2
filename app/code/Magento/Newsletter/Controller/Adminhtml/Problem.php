<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml;

/**
 * Newsletter subscribers controller
 */
class Problem extends \Magento\Backend\App\Action
{

    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Newsletter::problem');
    }
}
