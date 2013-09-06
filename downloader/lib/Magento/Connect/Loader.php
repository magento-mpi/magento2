<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for loader which using in the Rest
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Loader
{
    /**
     * Factory for HTTP client
     *
     * @param string|false $protocol  'curl'/'socket' or false for auto-detect
     * @return \Magento\HTTP\IClient|\Magento\Connect\Loader\Ftp
     */
    public static function getInstance($protocol='')
    {
        if ($protocol  == 'ftp') {
            return new \Magento\Connect\Loader\Ftp();
        } else {
            return \Magento\HTTP\Client::getInstance();
        }
    }
}
