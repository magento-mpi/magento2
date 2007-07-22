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
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function _initChildren()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_general');
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));
        $category = Mage::registry('category');
        
        $this->_setFieldset($category->getAttributes(), $fieldset);
        
        $fieldset->addField('parent_id', 'select', array(
            'name'  => 'parent_id',
            'label' => __('Parent Category'),
            'value' => $this->getRequest()->getParam('parent'),
            'values'=> $this->_getParentCategoryOptions(),
            'required' => true,
            'class' => 'required-entry'
            ), 
            'name'
        );

        $form->addValues($category->getData());
        
        $fieldset->addField('is_active', 'checkbox', array(
            'name'      => 'is_active',
            'label'     => __('Is Active'),
            'value'     => 1,
            'checked'   => $category->getIsActive()
            )
        );
        
        $this->setForm($form);
        
        return $this;
    }
    
    protected function _getParentCategoryOptions()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $tree->getCategoryCollection()->addAttributeToSelect('name');
        $nodes = $tree->load(1, 5)
            ->getTree()
                ->getNodes();

        $options = array();
        foreach ($nodes as $node) {
        	$options[] = array(
        	   'value' => $node->getId(),
        	   'label' => $node->getName(),
        	   'style' => 'padding-left:'.(10*$node->getLevel()).'px',
        	);
        }
        return $options;
    }
}
