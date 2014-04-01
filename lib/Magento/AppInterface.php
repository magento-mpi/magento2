<?php
/**
 * Application interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

interface AppInterface
{
    /**
     * Default application locale
     */
    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Magento version
     */
    const VERSION = '2.0.0.0-dev70';

    /**
     * Launch application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch();
}
