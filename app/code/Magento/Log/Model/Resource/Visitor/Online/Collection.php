<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Online visitors collection
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Log\Model\Resource\Visitor\Online;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * joined fields array
     *
     * @var array
     */
    protected $_fields   = array();

    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Log\Model\Visitor\Online', '\Magento\Log\Model\Resource\Visitor\Online');
    }

    /**
     * Add Customer data to collection
     *
     * @return \Magento\Log\Model\Resource\Visitor\Online\Collection
     */
    public function addCustomerData()
    {
        $customer   = \Mage::getModel('\Magento\Customer\Model\Customer');
        // alias => attribute_code
        $attributes = array(
            'customer_lastname'     => 'lastname',
            'customer_firstname'    => 'firstname',
            'customer_email'        => 'email'
        );

        foreach ($attributes as $alias => $attributeCode) {
            $attribute = $customer->getAttribute($attributeCode);
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */

            if ($attribute->getBackendType() == 'static') {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    array($alias => $attribute->getAttributeCode())
                );

                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attribute->getAttributeCode());
            }
            else {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $joinConds  = array(
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute->getAttributeId())
                );

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
                    join(' AND ', $joinConds),
                    array($alias => 'value')
                );

                $this->_fields[$alias] = sprintf('%s.value', $tableAlias);
            }
        }

        $this->setFlag('has_customer_data', true);
        return $this;
    }

    /**
     * Filter collection by specified website(s)
     *
     * @param int|array $websiteIds
     * @return \Magento\Log\Model\Resource\Visitor\Online\Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        if ($this->getFlag('has_customer_data')) {
            $this->getSelect()
                ->where('customer_email.website_id IN (?)', $websiteIds);
        }
        return $this;
    }

    /**
     * Add field filter to collection
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     *
     * @param string $field
     * @param null|string|array $condition
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (isset($this->_fields[$field])) {
            $field = $this->_fields[$field];
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
