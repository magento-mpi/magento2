<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product links collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Product object
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product Link model class
     *
     * @var Mage_Catalog_Model_Product_Link
     */
    protected $_linkModel;

    /**
     * Product Link Type identifier
     *
     * @var Mage_Catalog_Model_Product_Type
     */
    protected $_linkTypeId;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Catalog_Model_Product_Link', 'Mage_Catalog_Model_Resource_Product_Link');
    }

    /**
     * Declare link model and initialize type attributes join
     *
     * @param Mage_Catalog_Model_Product_Link $linkModel
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function setLinkModel(Mage_Catalog_Model_Product_Link $linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->hasLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }
        return $this;
    }

    /**
     * Retrieve collection link model
     *
     * @return Mage_Catalog_Model_Product_Link
     */
    public function getLinkModel()
    {
        return $this->_linkModel;
    }

    /**
     * Initialize collection parent product and add limitation join
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve collection base product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Add link's type to filter
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function addLinkTypeIdFilter()
    {
        if ($this->_linkTypeId) {
            $this->addFieldToFilter('link_type_id', array('eq' => $this->_linkTypeId));
        }
        return $this;
    }

    /**
     * Add product to filter
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function addProductIdFilter()
    {
        if ($this->getProduct() && $this->getProduct()->getId()) {
            $this->addFieldToFilter('product_id',  array('eq' => $this->getProduct()->getId()));
        }
        return $this;
    }

    /**
     * Join attributes
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function joinAttributes()
    {
        if (!$this->getLinkModel()) {
            return $this;
        }
        $attributes = $this->getLinkModel()->getAttributes();
        $adapter = $this->getConnection();
        foreach ($attributes as $attribute) {
            $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
            $alias = sprintf('link_attribute_%s_%s', $attribute['code'], $attribute['type']);

            $aliasInCondition = $adapter->quoteColumnAs($alias, null);
            $this->getSelect()->joinLeft(
                array($alias => $table),
                $aliasInCondition . '.link_id = main_table.link_id AND '
                    . $aliasInCondition . '.product_link_attribute_id = ' . (int) $attribute['id'],
                array($attribute['code'] => 'value')
            );
        }

        return $this;
    }
}
