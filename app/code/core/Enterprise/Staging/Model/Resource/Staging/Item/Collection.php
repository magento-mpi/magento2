<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging item collection
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Staging_Item', 'Enterprise_Staging_Model_Resource_Staging_Item');
    }

    /**
     * Set staging filter into collection
     *
     * @param mixed $stagingId (if object must be implemented getId() method)
     * @return Enterprise_Staging_Model_Resource_Staging_Item_Collection
     */
    public function setStagingFilter($stagingId)
    {
        if ($stagingId instanceof Varien_Object) {
            $stagingId = $stagingId->getId();
        }
        $this->addFieldToFilter('staging_id', (int) $stagingId);

        return $this;
    }

    /**
     * Retrieve item from collection where "code" attribute value equals to given code
     *
     * @param string $code
     * @return object Enterprise_Staging_Model_Staging_Item
     */
    public function getItemByCode($code)
    {
        foreach ($this->_items as $item) {
            if ($item->getCode() == (string) $code) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Convert items array to array for select options
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('staging_item_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     * array($value => $label)
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('staging_item_id', 'name');
    }
}
