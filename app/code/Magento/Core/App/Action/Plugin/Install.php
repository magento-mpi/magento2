<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class Install {
    public function beforeDispatch()
    {
        if (!$this->_appState->isInstalled()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_redirect('install');
            return;
        }
    }
} 