<?php

class Mage_Core_Controller_Zend_Dispatcher extends Zend_Controller_Dispatcher_Standard 
{
    protected $_realModuleName = array();

    public function addModule($module, $frontName)
    {
        $this->addControllerDirectory(Mage::getModuleDir('controllers', $module), $frontName);
        $this->_realModuleName[$frontName] = $module;
        return $this;
    }

    public function getRealModuleName($frontName=null)
    {
        if (null===$frontName) {
            $frontName = $this->_curModule;
        }
        if (isset($this->_realModuleName[$frontName])) {
            return $this->_realModuleName[$frontName];
        }
        return false;
    }

    public function formatModuleName($frontName)
    {
        return $frontName;
    }
    
    /**
     * Load a controller class
     * 
     * Attempts to load the controller class file from 
     * {@link getControllerDirectory()}.  If the controller belongs to a 
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className 
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className)
    {
        $dispatchDir = $this->getDispatchDirectory();

        $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);
        $dir         = dirname($loadFile);
        $file        = basename($loadFile);

        try {
            Zend_Loader::loadFile($file, $dir, true);
        } catch (Zend_Exception $e) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $file . '" in directory "' . $dir . '"');
        }

        $className = $this->getRealModuleName() . '_' . $className;

        if (!class_exists($className, false)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $className . '")');
        }

        return $className;
    }
}