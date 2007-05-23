<?php



/**
 * Zend html block 
 *
 * @package    Mage
 * @subpackage Core
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Template_Zend extends Mage_Core_Block_Template
{
    protected $_view = null;
   
    /**
     * Class constructor. Base html block
     * 
     * @param      none
     * @return     void
     * @author     Soroka Dmitriy <dmitriy@varien.com>
     */
    function _construct()
    {
        parent::_construct();
        $this->_view = new Zend_View();
    }
    
    public function assign($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            foreach ($key as $k=>$v) {
                $this->assign($k, $v);
            }
        } elseif (!is_null($value)) {
            $this->_view->assign($key, $value);
        }
        return $this;
    }
    
    public function setScriptPath($dir)
    {
        $this->_view->setScriptPath($dir.DS);
    }
    
    public function fetchView($fileName)
    {
        return $this->_view->render($fileName);
    }
}// Class Mage_Core_Block_Template END