<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap cms page collection model
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Resource_Cms_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource model (catalog/category)
     *
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }

    /**
     * Retrieve cms page collection array
     *
     * @param integer $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $pages = array();

        $select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName(),
                'identifier AS url', 'update_time as updated_at'))
            ->join(
                array('store_table' => $this->getTable('cms_page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )
            ->where('main_table.is_active = 1')
            ->where('main_table.identifier != ?', Mage_Cms_Model_Page::NOROUTE_PAGE_ID)
            ->where('store_table.store_id IN(?)', array(0, $storeId));

        $query = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @param array $data
     * @return Varien_Object
     */
    protected function _prepareObject(array $data)
    {
        $object = new Varien_Object();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);
        $object->setUpdatedAt($data['updated_at']);

        return $object;
    }
}
