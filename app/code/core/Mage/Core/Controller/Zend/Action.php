<?php

/**
 * Custom Zend_Controller_Action class
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Controller_Zend_Action extends Zend_Controller_Action
{
     /**
      * Action flags
      *
      * for example used to disable rendering default layout
      *
      * @var array
      */
     protected $_flags = array();
     protected $_layout = null;


     function getFlag($action, $flag='')
     {
         if (''===$action) {
             $action = $this->getRequest()->getActionName();
         }
         if (''===$flag) {
             return $this->_flags;
         } elseif (isset($this->_flags[$action][$flag])) {
             return $this->_flags[$action][$flag];
         } else {
             return false;
         }
     }

     function setFlag($action, $flag, $value)
     {
         if (''===$action) {
             $action = $this->getRequest()->getActionName();
         }
         $this->_flags[$action][$flag] = $value;
         return $this;
     }
     
     function getFullActionName()
     {
         return $this->getRequest()->getModuleName().'_'.
            $this->getRequest()->getControllerName().'_'.
            $this->getRequest()->getActionName();
     }
     
     function getLayout()
     {
         if (empty($this->_layout)) {
             $this->_layout = new Mage_Core_Layout();
         }
         return $this->_layout;
     }
     
     function loadLayout($area, $ids, $key='')
     {
        if (''===$key) {
            if (is_array($ids)) {
                Mage::exception('Please specify key for loadLayout('.$area.', Array('.join(',',$ids).'))');
            }
            $key = $area.'_'.$ids;
        }
         
        $layout = $this->getLayout();
        
        $layout->init($key);
        if (!$layout->isCacheLoaded()) {
            foreach ((array)$ids as $id) {
                $layout->loadUpdatesFromConfig($area, $id);
            }
            $layout->saveCache();
        }
        
        $layout->createBlocks();
        return $this;
     }
     
     function renderLayout()
     {
         Mage::dispatchEvent('beforeRenderLayout');
         Mage::dispatchEvent('beforeRenderLayout_'.$this->getFullActionName());
         
         $blocks = Mage_Core_Block::getOutputBlocks();

         if (!empty($blocks)) {
             $response = $this->getResponse();
             foreach ($blocks as $callback) {
                 $out = Mage::getBlock($callback[0])->$callback[1]();
                 $response->appendBody($out);
             }
         }
         return $this;
     }
     
    /**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }

        Mage::dispatchEvent('action_preDispatch');
        Mage::dispatchEvent('action_preDispatch_'.$this->getFullActionName());
    }

    /**
     * Dispatches event after action
     */
    function postDispatch()
    {
        if ($this->getFlag('', 'no-postDispatch')) {
            return;
        }
        
        Mage::dispatchEvent('action_postDispatch_'.$this->getFullActionName());
        Mage::dispatchEvent('action_postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Mage::dispatchEvent('action_noRoute');
    }
}