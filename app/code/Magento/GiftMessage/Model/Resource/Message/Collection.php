<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message collection
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Model\Resource\Message;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\GiftMessage\Model\Message', '\Magento\GiftMessage\Model\Resource\Message');
    }
}
