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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging dataset model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Log
{
    static $_logs = array();

    const LOG_CODE_ERROR    = 1;

    const LOG_CODE_NOTTICE  = 2;

    const LOG_CODE_SUCCESS  = 3;

    static public function addLog($action, $logMessage, $code = 'error')
    {
        self::$_logs[$code][$action] = $logMessage;
    }

    static public function getLogs()
    {
        $logs = array();
        foreach (self::$_logs as $code => $codeLogs) {
            switch ($code) {
                case self::LOG_CODE_ERROR:
                    $logs[] = Mage::helper('enterprise_staging')->__('Some errors was occured while processing:');
                    $logs[]= self::buildLogReport($codeLogs);
                    break;
                case self::LOG_CODE_NOTTICE:
                    $logs[] = Mage::helper('enterprise_staging')->__('Some non critical errors was occured while processing:');
                    $logs[]= self::buildLogReport($codeLogs);
                    break;
                case self::LOG_CODE_SUCCESS:
                    $logs[] = Mage::helper('enterprise_staging')->__('There are next messages was reported while processing:');
                    $logs[]= self::buildLogReport($codeLogs);
                    break;
                default:
                    $logs[] = Mage::helper('enterprise_staging')->__('There are unexpected unknown errors was occured while processing:');
                    $logs[]= self::buildLogReport($codeLogs);
                    break;
            }
        }
        $logs = implode("\n", $logs);
        return $logs;
    }

    static public function buildLogReport($logs)
    {
        if (!is_array($logs)) {
            $logs = array($logs);
        }
        $_logs = "";
        foreach ($logs as $log) {
            if ($log instanceof Enterprise_Staging_Exception) {
                $log = $log->getMessage();
            } elseif ($log instanceof Exception) {
                $log = $log->getMessage();
            } elseif (is_array($log)) {
                $log = implode("\n", $log);
            }
            $_logs .= "\n\n" . $log;
        }

        return $_logs;
    }
}