<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Model\Resource\Authorizenet;

/**
 * Resource Authorize.net debug model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Debug extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('authorizenet_debug', 'debug_id');
    }
}
