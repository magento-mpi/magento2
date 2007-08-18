<?php
/**
 * Tag model
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Magento Core ;-)
 */

class Mage_Tag_Model_Tag extends Mage_Core_Model_Abstract
{
    const STATUS_DISABLED = -1;
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    public function loadByName($name)
    {
        $this->getResource()->loadByName($this, $name);
        return $this;
    }

    public function getApprovedStatus()
    {
        return self::STATUS_APPROVED;
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getEntityCollection()
    {
        return Mage::getResourceModel('tag/product_collection');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('tag/customer_collection');
    }

    public function getTaggedProductsUrl()
    {
        return Mage::getUrl('tag/product/list', array('tagId' => $this->getId()));
    }

    public function getViewTagUrl()
    {
        return Mage::getUrl('tag/customer/view', array('tagId' => $this->getId()));
    }

    public function getEditTagUrl()
    {
        return Mage::getUrl('tag/customer/edit', array('tagId' => $this->getId()));
    }

    public function getRemoveTagUrl()
    {
        return Mage::getUrl('tag/customer/remove', array('tagId' => $this->getId()));
    }
}