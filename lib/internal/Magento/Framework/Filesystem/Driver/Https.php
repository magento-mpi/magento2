<?php
/**
 * Origin filesystem driver
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

        if (!isset($urlProp['port'])) {
            $urlProp['port'] = 443;
        }

        return $urlProp;
    }

    /**
     * Open a https url
     *
     * @param string $hostname
     * @param int $port
     * @return array
     */
    protected function open($hostname, $port)
    {
        return parent::open('ssl://' . $hostname, $port);
    }
}
