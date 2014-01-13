<?php
/**
 * Configuration Reinitable Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

interface ReinitableConfigInterface extends \Magento\App\ConfigInterface
{
    /**
     * Reinitialize config object
     *
     * @return ReinitableConfigInterface
     */
    public function reinit();
}
