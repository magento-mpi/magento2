<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Model\Resource\Api;

class Debug extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource constructor
     */
    protected function _construct()
    {
        $this->_init('googlecheckout_api_debug', 'debug_id');
    }
}
