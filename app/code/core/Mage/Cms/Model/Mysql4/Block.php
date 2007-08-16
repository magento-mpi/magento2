<?php
/**
 * CMS block model
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Cms_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('cms/block', 'block_id');
        $this->_uniqueFields = array( array(
            'field' => array('identifier', 'store_id'),
            'title' => __('Such a block identifier in selected store'),
        ));
    }

    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (! $object->getId()) {
            $object->setCreationTime(now());
        }
        $object->setUpdateTime(now());
        return $this;
    }
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (!intval($value) && is_string($value)) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }
}
