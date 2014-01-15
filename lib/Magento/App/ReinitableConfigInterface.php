<?php
/**
 * Configuration Reinitable Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

interface ReinitableConfigInterface extends \Magento\App\ConfigInterface
{
    /**
     * Reinitialize config object
     */
    public function reinit();
}
