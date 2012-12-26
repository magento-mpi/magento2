<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout update collection model
 */
class Mage_Core_Model_Resource_Layout_Update_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'layout_update_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'layout_update_collection';

    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Resource_Layout_Update');
    }

    /**
     * Add filter by theme id
     *
     * @param int $themeId
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    public function addThemeFilter($themeId)
    {
        $this->_joinWithLink();
        $this->getSelect()
            ->where('link.theme_id = ?', $themeId);

        return $this;
    }

    /**
     * Add filter by store id
     *
     * @param int $storeId
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->_joinWithLink();
        $this->getSelect()
            ->where('link.store_id = ?', $storeId);

        return $this;
    }

    /**
     * Join with layout link table
     *
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _joinWithLink()
    {
        if (!$this->getFlag('joined_with_link_table')) {
            $this->getSelect()
                ->join(
                    array('link' => 'core_layout_link'),
                    'link.layout_update_id = main_table.layout_update_id',
                    array('store_id', 'theme_id')
                );

            $this->setFlag('joined_with_link_table', true);
        }

        return $this;
    }
}
