<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource authorizenet debug model
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Model\Resource\Authorizenet;

class Debug extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('authorizenet_debug', 'debug_id');
    }
}
