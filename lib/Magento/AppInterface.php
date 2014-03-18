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
     * Retrieve cookie object
     *
     * @return \Magento\Stdlib\Cookie
     */
    public function getCookie();

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  \Magento\AppInterface
     */
    public function loadAreaPart($area, $part);

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  \Magento\Core\Model\App\Area
     */
    public function getArea($code);

    /**
     * Retrieve layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout();

    /**
     * Retrieve request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest();

}
