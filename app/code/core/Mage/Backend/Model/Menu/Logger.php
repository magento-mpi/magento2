<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu logger model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Menu_Logger extends Mage_Core_Model_Logger
{
    const MENU_DEBUG_FILE = 'menu-debug.log';

    /**
     * Log wrapper
     *
     * @param string $message
     * @param int $level
     * @param string $file
     * @param bool $forceLog
     * @return void
     */
    public function log($message, $level = null, $file = '', $forceLog = false)
    {
        if (empty($file)) {
            $file = self::MENU_DEBUG_FILE;
        }
        parent::log($message, $level, $file, $forceLog);
    }

    /**
     * Log exception wrapper
     *
     * @param Exception $exception
     */
    public function logException(Exception $exception)
    {
        $this->log("\n" . $exception->__toString(), Zend_Log::ERR);
    }
}
