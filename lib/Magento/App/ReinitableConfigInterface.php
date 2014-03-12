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

interface ReinitableConfigInterface extends ConfigInterface
{
    /**
     * Reinitialize config object
     *
     * @return $this
     */
    public function reinit();
}
