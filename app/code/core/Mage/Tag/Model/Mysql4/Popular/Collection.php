<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Popular tags collection model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Popular_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    public function joinFields($storeId = 0)
    {
        $this->getSelect()
            ->reset()
            ->from(array('main_table' => $this->getTable('tag/summary')))
            ->join(
                array('tag' => $this->getTable('tag/tag')), 
                'tag.tag_id = main_table.tag_id AND tag.status = '.Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->where('main_table.store_id = ?', $storeId)
            ->order('popularity desc');
       
        return $this;
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }
    
    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }
}