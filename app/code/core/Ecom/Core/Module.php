<?php
#include_once 'Ecom/Core/Module/Abstract.php';

/**
 * Enter description here...
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Ecom_Core_Module extends Ecom_Core_Module_Abstract
{
    protected $_info = array(
        'name'=>'Ecom_Core',
        'version'=>'0.1.0a3',
    );

    public function load()
    {
        Ecom::addObserver('Ecom_Core_Module::run.after', array(Ecom::getController(), 'run'));
    }
    
    public function run()
    {
        Ecom::dispatchEvent(__METHOD__.'.before');
        Ecom::dispatchEvent(__METHOD__.'.after');
    }
        
    protected function _addConfigSections()
    {
        Ecom::addConfigSection('resourceTypes', array('Ecom_Core_Resource', 'loadTypesConfig'));
        Ecom::addConfigSection('resources', array('Ecom_Core_Resource', 'loadResourcesConfig'));
        Ecom::addConfigSection('resourceEntities', array('Ecom_Core_Resource', 'loadEntitiesConfig'));
        Ecom::addConfigSection('resourceModels', array('Ecom_Core_Model', 'loadModelsConfig'));
        Ecom::addConfigSection('blockTypes', array('Ecom_Core_Block', 'loadTypesConfig'));
    }
}