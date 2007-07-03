<?php
/**
 * Admin page left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('page_form');
    }

    protected function _beforeToHtml()
    {
        $pageId = intval( $this->_request->getParam('page') );
        $pageObject = Mage::getModel('cms/page')->loadById($pageId);

        Varien_Profiler::start('pageForm');
        $this->addTab('main_section', array(
            'label'     => __('main page data'),
            'title'     => __('main page data title'),
            'content'   => $this->getLayout()->createBlock('adminhtml/cms_page_maintab')
                            ->setPageObject($pageObject)
                            ->toHtml(),
            'active'    => true
        ));

        $this->addTab('meta_section', array(
            'label'     => __('meta data'),
            'title'     => __('meta data title'),
            'content'   => $this->getLayout()->createBlock('adminhtml/cms_page_metatab')
                            ->setPageObject($pageObject)
                            ->toHtml(),
        ));

        Varien_Profiler::stop('pageForm');
        return parent::_beforeToHtml();
    }
}