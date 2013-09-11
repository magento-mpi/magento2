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
 * GiftCardAccount History Resource Collection
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\History;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\History', 'Magento\GiftCardAccount\Model\Resource\History');
    }
}
