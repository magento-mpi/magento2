<?php
/**
 * Product list toolbar
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_List_Toolbar extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/list/toolbar.phtml');
    }
    public function setViewBy($key, $values=array())
    {
        $this->_viewBy[$key] = $values;
        return $this;
    }

    public function getViewBy($key='')
    {
        if(is_array($this->_viewBy)) {
            if($key != '') {
                return $this->_viewBy[$key];
            }
            else {
                return $this->_viewBy;
            }
        }
        return false;
    }

    public function getIsViewBy($key, $value='')
    {
        if($value == '' && isset($this->_viewBy[$key])) {
            return true;
        }
        elseif($value != '' && in_array($value, $this->_viewBy[$key])) {
            return true;
        }
        return false;
    }
}
