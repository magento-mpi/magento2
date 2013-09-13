<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auth session model
 */
class Magento_Backend_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Init session
     *
     * @param Magento_Core_Model_logger $logger
     */
    public function __construct(Magento_Core_Model_Logger $logger)
    {
        $this->init('adminhtml');
        parent::__construct($logger);
    }
}
