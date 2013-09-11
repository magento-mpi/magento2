<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Url rewrite resource collection model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Url\Rewrite;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Url\Rewrite', 'Magento\Core\Model\Resource\Url\Rewrite');
    }

    /**
     * Filter collections by stores
     *
     * @param mixed $store
     * @param bool $withAdmin
     * @return \Magento\Core\Model\Resource\Url\Rewrite\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!is_array($store)) {
            $store = array(\Mage::app()->getStore($store)->getId());
        }
        if ($withAdmin) {
            $store[] = 0;
        }

        $this->addFieldToFilter('store_id', array('in' => $store));

        return $this;
    }

    /**
     *  Add filter by catalog product Id
     *
     * @param int $productId
     * @return \Magento\Core\Model\Resource\Url\Rewrite\Collection
     */
    public function filterAllByProductId($productId)
    {
        $this->getSelect()
            ->where('id_path = ?', "product/{$productId}")
            ->orWhere('id_path LIKE ?', "product/{$productId}/%");

        return $this;
    }

    /**
     * Add filter by all catalog category
     *
     * @return \Magento\Core\Model\Resource\Url\Rewrite\Collection
     */
    public function filterAllByCategory()
    {
        $this->getSelect()
            ->where('id_path LIKE ?', "category/%");
        return $this;
    }
}
