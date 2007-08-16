<?php
/**
 * Tags Customer Reviews Block
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Block_Customer_Recent extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tag/customer/list.phtml');

        $this->_collection = Mage::getModel('tag/tag')->getEntityCollection();

        $this->_collection
            #->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDescOrder()
            ->setPageSize(5)
            ->load()
            ->addProductTags();
    }

    public function count()
    {
        return $this->_collection->getSize();
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function dateFormat($date)
    {
         return strftime(Mage::getStoreConfig('general/local/date_format_short'), strtotime($date));
    }

    public function getAllTagsUrl()
    {
        return Mage::getUrl('tag/customer');
    }
}