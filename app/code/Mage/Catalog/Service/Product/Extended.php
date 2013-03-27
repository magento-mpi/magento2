<?php
class Mage_Catalog_Service_Product_Extended extends Mage_Catalog_Service_Product
{
    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;
    /** @var Mage_Catalog_Helper_Product */
    protected $_productHelper;

    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_Factory_Helper $helperFactory)
    {
        parent::__construct($objectManager, $helperFactory->get('Mage_Catalog_Helper_Product'));
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Returns only links of the product.
     *
     * @param string $productIdOrSku
     * @return array
     */
    public function getLinks($productIdOrSku)
    {
        if (empty($this->_productHelper)) {
            $this->_productHelper = $this->_helperFactory->get('Mage_Catalog_Helper_Product');
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_productHelper->getProduct($productIdOrSku, null);

        if (!$product->getId()) {
            return array();
        }

        $data = $this->_getMapper()->apply(
            array(),
            $product,
            array('wishlistEnabled', 'wishListAddUrl', 'compareAddUrl', 'emailToFriendUrl')
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @return Mage_Catalog_Service_Product_Extended_Mapper
     */
    protected function _getMapper()
    {
        if (empty($this->_mapper)) {
            $this->_mapper = $this->_objectManager->create('Mage_Catalog_Service_Product_Extended_Mapper');
        }

        return $this->_mapper;
    }
}
