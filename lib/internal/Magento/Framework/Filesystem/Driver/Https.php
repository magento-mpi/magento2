<?php
/**
 * Origin filesystem driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Driver;

/**
 * Class Https
 *
 */
class Https extends Http
{
    /**
     * Scheme distinguisher
     *
     * @var string
     */
    protected $scheme = 'https';

    /**
     * Parse a https url
     *
     * @param string $path
     * @return array
     */
    protected function parseUrl($path)
    {
        $urlProp = parent::parseUrl($path);

        if ($urlProp['scheme'] === 'https') {
            if (!isset($urlProp['port'])) {
                $urlProp['port'] = 443;
            }
        }

        return $urlProp;
    }

    /**
     * Open a https url
     *
     * @param $hostname
     * @param $port
     * @return array
     */
    protected function open($hostname, $port)
    {
        return parent::open('ssl://' . $hostname, $port);
    }
}
