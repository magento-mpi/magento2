<?php
/**
 * Adminhtml menu block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page_Menu extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('adminhtml/page/menu.phtml');
    }
    
    protected function _beforeToHtml()
    {
        $menu = array(
            'dashboard' => array(
                'label' => __('dashboard'),
                'title' => __('dashboard title'),
                'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
            ),
            'system'    => array(
                'label' => __('system'),
                'title' => __('system title'),
                'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                'active'=> true
            ),
            'customer'  => array(
                'label' => __('customer'),
                'title' => __('customer title'),
                'url'   => Mage::getUrl('adminhtml', array('controller'=>'customer', 'action'=>'index')),
            ),
            'catalog'   => array(
                'label' => __('catalog'),
                'title' => __('catalog title'),
                'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                'children'  => array(
                    'products'  => array(
                        'label' => __('products'),
                        'title' => __('products title'),
                        'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                    ),
                    'attributes'=> array(
                        'label' => __('attributes'),
                        'title' => __('attributes title'),
                        'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                    )
                )
            ),
            'sales'     => array(
                'label' => __('sales'),
                'title' => __('sales title'),
                'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                'children'  => array(
                    'orders'    => array(
                        'label' => __('orders'),
                        'title' => __('orders title'),
                        'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                    ),
                    'qoutes'    => array(
                        'label' => __('qoutes'),
                        'title' => __('qoutes title'),
                        'url'   => Mage::getUrl('adminhtml', array('controller'=>'index', 'action'=>'layoutFrame')),
                    )
                )
            )
        );
        $this->assign('menu', $menu);
        return true;
    }
}
