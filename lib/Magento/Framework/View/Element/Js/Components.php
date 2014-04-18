<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element\Js;

use Magento\Framework\App\State;
use Magento\Framework\View\Element\Template;

class Components extends Template
{
    /**
     * Developer mode
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == State::MODE_DEVELOPER;
    }
}
