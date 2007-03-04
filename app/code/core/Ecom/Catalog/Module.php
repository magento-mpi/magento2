<?php
#include_once 'Ecom/Core/Module/Abstract.php';
#include_once 'Ecom/Catalog/Price/Rule.php';

class Ecom_Catalog_Module extends Ecom_Core_Module_Abstract
{
    protected $_info = array(
        'name'=>'Ecom_Catalog',
        'version'=>'0.1.0a8',
    );

    function load()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }

    function updateLayout()
    {
        $updateLayout = array(':catalog.layout.update', 
            array('#head', array('>append', array('+tag_css', array('>setHref', '/catalog/style.css')))),
            array('#top.forms', array('>append', array('+tpl', '#search.form.mini', array('>setViewName', 'Ecom_Catalog', 'search.form.mini')))),
            array('#left', array('>insert', array('+tpl', '#catalog.leftnav', array('>setViewName', 'Ecom_Catalog', 'leftnav'),
                array('>setChild', 'bytopic', array('+catalog_category_list', '#.bytopic', array('>loadCategories', 2))),
                array('>setChild', 'byproduct', array('+catalog_category_list', '#.byproduct', array('>loadCategories', 13))),
            ))),
        );
        
        Ecom_Core_Block::loadArray($updateLayout);
    }
    
    protected function _addConfigSections()
    {
        Ecom::addConfigSection('priceRuleConditionTypes', array('Ecom_Catalog_Price_Rule', 'loadConditionTypesConfig'));
        Ecom::addConfigSection('priceRuleActionTypes', array('Ecom_Catalog_Price_Rule', 'loadActionTypesConfig'));
    }
}