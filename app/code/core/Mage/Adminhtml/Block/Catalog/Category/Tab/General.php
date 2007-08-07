<?php
/**
 * Category edit general tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_category;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }
    
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }
    
    public function _initChildren()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_general');
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));
//        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('adminhtml/widget_form_element'));
        
        $this->_setFieldset($this->getCategory()->getAttributes(), $fieldset);
        
        if (!$this->getCategory()->getId()) {
            $fieldset->addField('parent_id', 'select', array(
                'name'  => 'parent_id',
                'label' => __('Parent Category'),
                'value' => $this->getRequest()->getParam('parent'),
                'values'=> $this->_getParentCategoryOptions(),
                //'required' => true,
                //'class' => 'required-entry'
                ), 
                'name'
            );
        }

        $form->addValues($this->getCategory()->getData());
        
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
        
        return $this;
    }
    
    protected function _getParentCategoryOptions($node=null, &$options=array())
    {
        if (is_null($node)) {
            $node = $this->getLayout()->getBlock('category.tree')->getRoot();
        }
        
        if ($node) {
            $options[] = array(
               'value' => $node->getId(),
               'label' => $node->getName(),
               'style' => 'padding-left:'.(10*$node->getLevel()).'px',
            );
            
            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }
}
