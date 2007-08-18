<?php
/**
 * Adminhtml tags page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tag/index.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', Mage::getUrl('adminhtml/tag/new'));
        $this->setChild('tag_frame', $this->getLayout()->createBlock('adminhtml/tag_tab_all', 'tag.frame'));
        return parent::_beforeToHtml();
    }
}
