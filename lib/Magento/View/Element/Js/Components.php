<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Js;

class Components extends \Magento\View\Element\Template
{
    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == \Magento\App\State::MODE_DEVELOPER;
    }
}
