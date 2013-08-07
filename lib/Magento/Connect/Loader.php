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
     * @param string/false $protocol  'curl'/'socket' or false for auto-detect
     * @return Magento_HTTP_Client/Magento_Connect_Loader_Ftp
     */
    public static function getInstance($protocol='')
    {
        if ($protocol  == 'ftp') {
            return new Magento_Connect_Loader_Ftp();
        } else {
            return Magento_HTTP_Client::getInstance();
        }
    }

}