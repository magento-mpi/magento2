<?php
/**
 * Category tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Widget_Tabs 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => __('General information'),
            'content'   => 'general',
            'active'    => true
        ));

        $this->addTab('products', array(
            'label'     => __('Category products'),
            'content'   => 'products'
        ));

        $this->addTab('features', array(
            'label'     => __('Feature products'),
            'content'   => 'Feature products'
        ));        
        return parent::_beforeToHtml();
    }
}
