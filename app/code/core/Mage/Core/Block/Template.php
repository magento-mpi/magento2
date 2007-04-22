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
    protected $_viewDir = '';
    protected $_viewVars = array();
    
    /**
     * Set block template
     * 
     * @param     string $file
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function setTemplate($templateName)
    {
        #echo "<hr>Module:"; print_r($viewModule); echo ", Name:"; print_r($viewName);
        #$this->setAttribute('viewModule', $viewModule);
        $this->setAttribute('templateName', $templateName);
        return $this;
    }
    
    public function assign($key, $value=null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->assign($k, $v);
            }
        } 
        else {
            $this->_viewVars[$key] = $value;
        }
        return $this;
    }
    
    public function setScriptPath($dir)
    {
        $this->_viewDir = $dir;
    }
    
    public function fetchView($fileName)
    {
        extract ($this->_viewVars);
        ob_start();
        include $this->_viewDir.DS.$fileName;
        return ob_get_clean();
    }
    
    /**
     * Render block
     *
     * @return unknown
     */
    public function renderView()
    {
        $templatesDir = Mage::getBaseDir('template');

        $this->assign('baseUrl', Mage::getBaseUrl());
        $this->assign('baseSkinUrl', Mage::getBaseUrl(array('_type'=>'skin')));
        $this->assign('baseJsUrl', Mage::getBaseUrl(array('_type'=>'js')));
        $this->assign('templatesDir', $templatesDir);
        $this->assign('currentUrl', Mage::registry('controller')->getRequest()->getRequestUri());
        $this->assign('currentBlock', $this);
        
        $this->setScriptPath($templatesDir.DS);
        $html = $this->fetchView($this->getAttribute('templateName'));
        
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