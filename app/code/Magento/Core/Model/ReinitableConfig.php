<?php
/**
 * Application configuration used to re-initialize config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class ReinitableConfig extends \Magento\Core\Model\Config implements \Magento\App\ReinitableConfigInterface
{
    /**
     * Reinitialize configuration
     *
     * @return ReinitableConfigInterface
     */
    public function reinit()
    {
        $this->_sectionPool->clean();
        return $this;
    }
}
