<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Pool Resource Model Collection
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\Pool;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\Pool', 'Magento\GiftCardAccount\Model\Resource\Pool');
    }
}
