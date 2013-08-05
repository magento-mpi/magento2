<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Catalog Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Search_Catalog extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Catalog
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $collection = Mage::helper('Mage_CatalogSearch_Helper_Data')->getQuery()->getSearchCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $product) {
            $description = strip_tags($product->getDescription());
            $arr[] = array(
                'id'            => 'product/1/'.$product->getId(),
                'type'          => __('Product'),
                'name'          => $product->getName(),
                'description'   => Mage::helper('Mage_Core_Helper_String')->substr($description, 0, 30),
                'url' => Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl(
                    '*/catalog_product/edit',
                    array(
                        'id' => $product->getId()
                    )
                ),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
