<?php
/**
 * Popular tags block
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Block_Popular extends Mage_Core_Block_Template
{

    protected $_tags;
    protected $_minPopularity;
    protected $_maxPopularity;

    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = array();
            $tags = Mage::getResourceModel('tag/tag_collection')
                ->addPopularity(20)
                ->setOrder('popularity', 'DESC')
                ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
                ->load()
                ->getItems()
            ;
        }

        if( count($tags) == 0 ) {
            return;
        }

        $this->_maxPopularity = $tags[0]->getPopularity();
        $this->_minPopularity = $tags[count($tags)-1]->getPopularity();
        $range = $this->_maxPopularity - $this->_minPopularity;
        $range = ( $range == 0 ) ? 1 : $range;
        foreach ($tags as $tag) {
            if( !$tag->getPopularity() ) {
                continue;
            }
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

    public function toHtml()
    {
        if (count($this->getTags()) > 0) {
            return parent::toHtml();
        }
        return '';
    }
}
