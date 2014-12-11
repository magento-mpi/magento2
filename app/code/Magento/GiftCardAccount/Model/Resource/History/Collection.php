<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Model\Resource\History;

/**
 * GiftCardAccount History Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\History', 'Magento\GiftCardAccount\Model\Resource\History');
    }
}
