<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Js;

class Components extends \Magento\View\Block\Template
{
    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == \Magento\App\State::MODE_DEVELOPER;
    }
}
