<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Js;

use Magento\App\State;
use Magento\View\Element\Template;

class Components extends Template
{
    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == State::MODE_DEVELOPER;
    }
}
