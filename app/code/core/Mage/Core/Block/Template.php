<?php



/**
 * Base html block 
 *
 * @package    Mage
 * @subpackage Core
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Template extends Mage_Core_Block_Abstract 
{
    protected $_view = null;
   
    /**
     * Class constructor. Base html block
     * 
     * @param      none
     * @return     void
     * @author     Soroka Dmitriy <dmitriy@varien.com>
     */
    function __construct($attributes = array())
    {
        parent::__construct($attributes);
    }
    
    public function setView()
    {
        if (empty($this->_view)) {
            $this->_view = new Mage_Core_View_Zend();
        }
    }
    
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Set block template
     * 
     * @param     string $file
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function setViewName($viewModule, $viewName)
    {
        $this->setAttribute('viewModule', $viewModule);
        $this->setAttribute('viewName', $viewName);
        return $this;
    }
    
    public function assign($key, $value=null)
    {
        $this->setView();
        
        if (is_array($key) && is_null($value)) {
            foreach ($key as $k=>$v) {
                $this->assign($k, $v);
            }
        } elseif (!is_null($value)) {
            $this->getView()->assign($key, $value);
        }
        return $this;
    }
    
    /**
     * Render block
     *
     * @return unknown
     */
    public function renderView()
    {       
        $viewModule = Mage::getModuleInfo($this->getAttribute('viewModule'));
        
        if (!$viewModule instanceof Mage_Core_Module_Info) {
            Mage::exception('Invalid view module name specified in block '.$this->getInfo('name').': '.$this->getAttribute('viewModule'));
        }

        $this->getView()->setScriptPath($viewModule->getRoot('views').DS);
        
        $this->getView()->assign('moduleImagesUrl', $viewModule->getBaseUrl('skin') . '/images');
        $this->getView()->assign('moduleSkinUrl', $viewModule->getBaseUrl('skin'));
        $this->getView()->assign('moduleViewsDir', $viewModule->getRoot('views'));
        
        $this->getView()->assign('curentBlock', $this);
        
        $html = $this->getView()->render($this->getAttribute('viewName').'.phtml');
        
        return $html;
    }
    
    /**
     * Before assign child block actions
     *
     * @param string $blockName
     */
    protected function _beforeAssign($blockName, $blockObject)
    {
    	// before assign child block actions
    }
    
    /**
     * Render block HTML
     *
     * @return string
     */
    public function toString()
    {
        $this->setView();

        if (!empty($this->_children)) {
            // Render child elements
            foreach ($this->_children as $name=>$block) {
                if ($block instanceof Mage_Core_Block_Abstract) {
                   $childHtml = $block->toString();
                } elseif (is_string($block)) {
                   $childHtml = $block;
                }
                
                $this->_beforeAssign($name, $block);
                $this->assign($name, $childHtml);
            }
        }

        return $this->renderView();
    }
}// Class Mage_Core_Block_Template END