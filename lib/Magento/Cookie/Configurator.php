<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cookie;

interface Configurator
{
    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getLifetime();

    /**
     * Retrieve Domain for cookie
     *
     * @return string
     */
    public function getDomain();

    /**
     * Retrieve use HTTP only flag
     *
     * @return bool
     */
    public function getHttponly();

    /**
     * Retrieve Path for cookie
     *
     * @return string
     */
    public function getPath();
}