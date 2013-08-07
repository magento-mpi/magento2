<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search test connection controller
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Controller_Adminhtml_Search_System_Config_Testconnection
    extends Magento_Adminhtml_Controller_Action
{
    /**
     * Check for connection to server
     */
    public function pingAction()
    {
        if (!isset($_REQUEST['host']) || !($host = $_REQUEST['host'])
            || !isset($_REQUEST['port']) || !($port = (int)$_REQUEST['port'])
            || !isset($_REQUEST['path']) || !($path = $_REQUEST['path'])
        ) {
            echo 0;
            die;
        }

        $pingUrl = 'http://' . $host . ':' . $port . '/' . $path . '/admin/ping';

        if (isset($_REQUEST['timeout'])) {
            $timeout = (int)$_REQUEST['timeout'];
            if ($timeout < 0) {
                $timeout = -1;
            }
        } else {
            $timeout = 0;
        }

        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'HEAD',
                    'timeout' => $timeout
                )
            )
        );

        // attempt a HEAD request to the solr ping page
        $ping = @file_get_contents($pingUrl, false, $context);

        // result is false if there was a timeout
        // or if the HTTP status was not 200
        if ($ping !== false) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
