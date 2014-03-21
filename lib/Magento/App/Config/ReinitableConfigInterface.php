<?php
/**
 * Configuration Reinitable Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Config;

interface ReinitableConfigInterface extends \Magento\App\Config\MutableScopeConfigInterface
{
    /**
     * Reinitialize config object
     *
     * @return \Magento\App\Config\ReinitableConfigInterface
     */
    public function reinit();
}
