<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class StoreCheck
{
    public function beforeDispatch()
    {
        // Prohibit disabled store actions
        if (!$this->_storeManager->getStore()->getIsActive())
        {
            $this->_storeManager->throwStoreException();
        }
    }
} 