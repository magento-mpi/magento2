<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\AbstractAction;

/**
 * System admin controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class System extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::system');
    }
}
