<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for loader which using in the Rest
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Loader
{
    /**
     * Factory for HTTP client
     *
     * @param string|false $protocol  'curl'/'socket' or false for auto-detect
     * @return Magento_HTTP_IClient|Magento_Connect_Loader_Ftp
     */
    public static function getInstance($protocol='')
    {
        if ($protocol  == 'ftp') {
            return new Magento_Connect_Loader_Ftp();
        } else {
            return Mage_HTTP_Client::getInstance();
        }
    }
}
