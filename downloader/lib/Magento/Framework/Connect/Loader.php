<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for loader which using in the Rest
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Connect;

class Loader
{
    /**
     * Factory for HTTP client
     *
     * @param string|false $protocol  'curl'/'socket' or false for auto-detect
     * @return \Magento\Framework\HTTP\IClient|\Magento\Framework\Connect\Loader\Ftp
     */
    public static function getInstance($protocol = '')
    {
        if ($protocol == 'ftp') {
            return new \Magento\Framework\Connect\Loader\Ftp();
        } else {
            return \Magento\Framework\HTTP\Client::getInstance();
        }
    }
}
