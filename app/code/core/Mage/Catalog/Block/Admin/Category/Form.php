<?php
/**
 * Category data form
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Category_Form extends Mage_Core_Block_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Core', 'form');
        $this->setAttribute('legend', 'Category form');
        $this->setAttribute('class', 'x-form');
        $this->addField('text1', 'text', array('name'=>'text1', 'id'=>'text1', 'value'=>11, 'label'=>'My field'));
        $this->addField('text3', 'select', array('name'=>'text3', 'id'=>'text3', 'value'=>11,'label'=>'Select field', 'values'=>array(0=>array('value'=>1, 'label'=>'1111111'))));
    }
}