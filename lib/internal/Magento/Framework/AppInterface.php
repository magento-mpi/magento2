<?php
/**
 * Application interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

interface AppInterface
{
    /**
     * Default application locale
     */
    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Magento version
     */
    const VERSION = '0.1.0-alpha94';

    /**
     * Launch application
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function launch();
}
