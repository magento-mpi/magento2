<?php
/**
 * Application configuration used to re-initialize config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class ReinitableConfig extends \Magento\App\Config implements \Magento\App\ReinitableConfigInterface
{
    /**
     * (@inheritdoc)
     */
    public function reinit()
    {
        $this->_scopePool->clean();
        return $this;
    }
}
