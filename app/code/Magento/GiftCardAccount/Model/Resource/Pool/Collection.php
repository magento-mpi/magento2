<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Resource\Pool;

/**
 * GiftCardAccount Pool Resource Model Collection
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\Pool', 'Magento\GiftCardAccount\Model\Resource\Pool');
    }
}
