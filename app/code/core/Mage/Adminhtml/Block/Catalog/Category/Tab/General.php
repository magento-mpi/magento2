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
        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('adminhtml/catalog_category_form_image'));
        
        $this->_setFieldset($this->getCategory()->getAttributes(), $fieldset);
        
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

        $form->addValues($this->getCategory()->getData());
        
        $fieldset->addField('is_active', 'checkbox', array(
            'name'      => 'is_active',
            'label'     => __('Is Active'),
            'value'     => 1,
            'checked'   => $this->getCategory()->getIsActive()
            )
        );
        
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
        
        return $this;
    }
    
    protected function _getParentCategoryOptions()
    {
        $root = $this->getLayout()->getBlock('category.tree')->getRootNode();
        $options = array();
        foreach ($root->getTree()->getNodes() as $node) {
            $options[] = array(
               'value' => $node->getId(),
               'label' => $node->getName(),
               'style' => 'padding-left:'.(10*($node->getLevel()-$root->getLevel())).'px',
            );
        }
        return $options;
    }
}
