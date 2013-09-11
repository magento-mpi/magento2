<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SalesRule Model Resource Rule Customer_Collection
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Resource\Rule\Customer;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\SalesRule\Model\Rule\Customer', 'Magento\SalesRule\Model\Resource\Rule\Customer');
    }
}
