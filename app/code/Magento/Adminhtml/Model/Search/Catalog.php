<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Catalog Model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Model\Search;

class Catalog extends \Magento\Object
{
    /**
     * Load search results
     *
     * @return \Magento\Adminhtml\Model\Search\Catalog
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $collection = \Mage::helper('Magento\CatalogSearch\Helper\Data')->getQuery()->getSearchCollection()
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
                'description'   => \Mage::helper('Magento\Core\Helper\String')->substr($description, 0, 30),
                'url' => \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl(
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
