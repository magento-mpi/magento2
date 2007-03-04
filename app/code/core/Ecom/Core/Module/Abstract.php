<?php

abstract class Ecom_Core_Module_Abstract
{


    protected $_setup = null;

    public function __construct($loadConfig=false)
    {
        if ($loadConfig) {
            $this->loadConfig();
        }
        
        $this->load();
    }

    /**
     * Get module information
     *
     * @return Ecom_Core_Module_Info
     */
    public function getModuleInfo()
    {
        return Ecom::getModuleInfo($this->getInfo('name'));
    }

    


}