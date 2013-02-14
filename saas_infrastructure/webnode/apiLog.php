<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Callback to be called on the php script shutdown to log API calls into the database
 *
 * @param $tenantId
 * @param $startTime
 */
function apiLogCallback($tenantId, $startTime) {
    try {
        // get stop time, duration
        $stopTime = microtime(true);
        $timestamp = intval($startTime);
        $duration = $stopTime - $startTime;

        // get $protocol
        preg_match('#^(/index\.php)?/api/([^/]+)/?#ims', $_SERVER['REQUEST_URI'], $res);
        $protocol = isset($res[2]) ? $res[2] : '';

        // get request and response
        $request = file_get_contents('php://input');
        $response = ob_get_contents();
        if (class_exists('Mage') && !$response) {
            $response = Mage::app()->getResponse()->getBody();
        }


        // get php error
        $error = serialize((array)error_get_last());

        // get ip
        $ip = ip2long($_SERVER['REMOTE_ADDR']);

        // insert record into database
        /* $var $dbh PDO */
        $dbh = new PDO(TMT_LOG_DSN, TMT_LOG_USER, TMT_LOG_PASSWORD, array(PDO::ATTR_TIMEOUT => 1));
        $sql = 'INSERT INTO magento_api_log SET time = ?, tenant_id = ?, proto = ?, request = ?, response = ?, '
            . 'error = ?, duration = ?, ip = ?';
        /* $var $dbh PDOStatement */
        $insert = $dbh->prepare($sql, array(PDO::ATTR_TIMEOUT => 2));
        $insert->execute(array($timestamp, $tenantId, $protocol, $request, $response, $error, $duration, $ip));
    } catch (Exception $e) {
    }
}

if (preg_match('#^(/index\.php)?/api/#ims', $_SERVER['REQUEST_URI']) && defined('TMT_LOG_DSN')
    && defined('TMT_LOG_USER') && defined('TMT_LOG_PASSWORD')
) {
    // get start time
    $startTime = microtime(true);

    // get tenant ID
    $tenantId = !empty($tenant) ? $tenant->getId() : '';

    // register callback
    register_shutdown_function("apiLogCallback", $tenantId, $startTime);
}
