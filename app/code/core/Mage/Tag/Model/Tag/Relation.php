<?php
/**
 * Tag relation model
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Model_Tag_Relation extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag_relation');
    }

    public function loadByTagCustomer($productId=null, $tagId, $customerId)
    {
        $this->setProductId($productId);
        $this->setTagId($tagId);
        $this->setCustomerId($customerId);

        $this->getResource()->loadByTagCustomer($this);
        return $this;
    }
}