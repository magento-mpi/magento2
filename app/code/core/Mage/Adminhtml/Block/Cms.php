<?php
/**
 * CMS control block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/cms/index.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', Mage::getUrl('adminhtml/cms_page/newpage'));
        $this->assign('grid', $this->getLayout()->createBlock('adminhtml/cms_grid', 'cms.grid')->toHtml());
        return $this;
    }
}