<?php
/**
 * adminhtml tags left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_tabs');
        // $this->setDestElementId('tag_frame');
        // $this->setTitle(__('Tags Management'));
        $this->addTab('all', array('label' => __('All Tags'), 'url' => $this->getUrl('*/*/all')))
            ->addTab('pending', array('label' => __('Pending Tags'), 'url' => $this->getUrl('*/*/pending')))
            ->addTab('customers', array('label' => __('Customers'), 'url' => $this->getUrl('*/*/customers')))
            ->addTab('products', array('label' => __('Products'), 'url' => $this->getUrl('*/*/products')))
        ;
    }

}
