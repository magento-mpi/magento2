<?php
/**
 * Admin CMS page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Page extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/cms/page/form.phtml');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller'=>'cms_page', 'action'=>'save'));
    }

    protected function _beforeToHtml()
    {
        $this->assign('header', __('manage page'));
        return $this;
    }
}