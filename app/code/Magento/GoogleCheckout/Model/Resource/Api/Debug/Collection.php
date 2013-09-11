<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Model\Resource\Api\Debug;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init('Magento\GoogleCheckout\Model\Api\Debug', 'Magento\GoogleCheckout\Model\Resource\Api\Debug');
    }
}
