<?php
/**
 * Tags list in customer's account
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Block_Customer_Tags extends Mage_Core_Block_Template
{
    protected $_tags;
    protected $_minPopularity;
    protected $_maxPopularity;

    public function __construct()
    {
        $this->setTemplate('tag/customer/tags.phtml');
    }

    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = array();

            $tags = Mage::getResourceModel('tag/tag_collection')
                ->addPopularity()
                ->setOrder('popularity', 'DESC')
                #->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->load()
                ->getItems();
        } else {
            return;
        }

        if( isset($tags) && count($tags) == 0 ) {
            return;
        }

        $this->_maxPopularity = $tags[0]->getPopularity();
        $this->_minPopularity = $tags[count($tags)-1]->getPopularity();
        $range = $this->_maxPopularity - $this->_minPopularity;
        $range = ( $range == 0 ) ? 1 : $range;

        foreach ($tags as $tag) {
            $tag->setRatio(($tag->getPopularity()-$this->_minPopularity)/$range);
            $this->_tags[$tag->getName()] = $tag;
        }
        ksort($this->_tags);
    }

    public function getTags()
    {
        $this->_loadTags();
        return $this->_tags;
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
}