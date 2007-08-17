<?php
/**
 * Customer's tags edit block
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Block_Customer_Edit extends Mage_Core_Block_Template
{
    protected $_tag;

    public function __construct()
    {
        $this->setTemplate('tag/customer/edit.phtml');
    }

    public function getTag()
    {
        if( !$this->_tag ) {
            $this->_tag = Mage::registry('tagModel');
        }

        return $this->_tag;
    }

    public function getFormAction()
    {
        return Mage::getUrl('*/*/save', array('tagId' => $this->getTag()->getTagId()));
    }
}