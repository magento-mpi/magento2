<?php

/**
 * Base Content Block class
 * 
 * For block generation you must define Data source class, data source class method,
 * parameters array and block template
 *
 * @package    Mage
 * @subpackage Core
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 03:59:31 EET 2007
 */

abstract class Mage_Core_Block_Abstract extends Varien_Data_Object
{
    /**
     * Parent layout of the block
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout = null;   

    /**
     * Contains references to child block objects
     *
     * @var array
     */
    protected $_children = array();

    /**
     * Constructor
     * 
     * @param     string $template
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->_construct();
    }
    
    protected function _construct()
    {
        
    }
    
    public function setLayout(Mage_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }
    
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Set child block
     * 
     * @param     string $name
     * @param     mixed  $block
     * @return    Mage_Core_Block_Abstract
     */

    public function getAttribute($name)
    {
        return $this->getData($name);
    }

    public function setAttribute($name, $value=null)
    {
        return $this->setData($name, $value);
    }
    
    public function setChild($name, $block)
    {
        if (is_string($block)) {
            $block = $this->getData('layout')->getBlock($block);
            if (!$block) {
                Mage::exception('Invalid block name to set child '.$name.': '.$block);
            }
        }
        
        if ($block->getData('is_anonymous')) {
            
            $suffix = $block->getData('anon_suffix');
            if (empty($suffix)) {
                $suffix = 'child'.sizeof($this->_children);
            }
            $blockName = $this->getData('name').'.'.$suffix;

            $this->getData('layout')->unsetBlock($block->getData('name'));
            $this->getData('layout')->setBlock($blockName, $block);
            
            $block->setData($blockName);
            $block->setData('is_anonymous', false);
            
            if (empty($name)) {
               $name = $blockName;
            }
        }
        
        $block->setData('parent', array('var'=>$name, 'block'=>$this));
        $this->_children[$name] = $block;

        return $this;
    }
    
    public function unsetChild($name)
    {
        unset($this->_children[$name]);
        $list = $this->getData('sorted_children_list');
        $key = array_search($name, $list);
        if (!empty($key)) {
            unset($list[$key]);
            $this->setData('sorted_children_list', $list);
        }
    }
    
    public function unsetChildren()
    {
        $this->_children = array();
        $this->setData('sorted_children_list', array());
        return $this;
    }

    /**
     * Get child block
     *
     * @param  sting $name
     * @return mixed
     */
    public function getChild($name='') 
    {
        if (''===$name) {
            return $this->_children;
        } else if (isset($this->_children[$name])) {
            return $this->_children[$name];
        }
        return false;
    }

    /**
     * Used only in lists
     * 
     * @author  Moshe Gurvich <moshe@varien.com>
     * @param   Mage_Core_Block_Abstract $block
     * @param   string $siblingName
     * @param   boolean $after
     * @return  object $this
     */
    function insert($block, $siblingName='', $after=false)
    {
        if ($block->getData('is_anonymous')) {
            $this->setChild('', $block);        
            $name = $block->getData('name');
        } else {
            $name = $block->getData('name');
            $this->setChild($name, $block);        
        }
        
        $list = $this->getData('sorted_children_list');
        if (empty($list)) {
            $list = array();
        }
    
        if (''===$siblingName) {
            if ($after) {
                array_push($list, $name);
            } else {
                array_unshift($list, $name);
            }
        } else {
            $key = array_search($siblingName, $list);
            if (false!==$key) {
                if ($after) {
                  $key++;
                }
                array_splice($list, $key, 0, $name);
            }
        }
        
        $this->setData('sorted_children_list', $list);
        
        return $this;
    }
    
    function append($block)
    {
        $this->insert($block, '', true);
        return $this;
    }

    
    /**
     * Convert _attributes array to string
     *
     * @param   array  $requiredAttributes
     * @param   string $separator
     * @param   string $valueQuote
     * @return  string
     */
    protected function _attributesToString($requiredAttributes=array(), $separator=' ', $valueQuote='"', $escapeValue=true)
    {
        $arrValues = array();
        if (is_array($requiredAttributes) && !empty($requiredAttributes)) {
            foreach ($requiredAttributes as $attribute) {
                $attributeValue = $this->getAttribute($attribute);
                if (!is_null($attributeValue)) {
                    $attributeValue = $escapeValue ? htmlspecialchars((string)$attributeValue, ENT_COMPAT) : $attributeValue;
                    $arrValues[] = $attribute . '=' . $valueQuote . $attributeValue . $valueQuote;
                }
            }
        }
        else {
            foreach ($this->_attributes as $attribute => $attributeValue) {
                $attributeValue = $escapeValue ? htmlspecialchars($attributeValue, ENT_COMPAT) : $attributeValue;
                $arrValues[] = $attribute . '=' . $valueQuote . $attributeValue . $valueQuote;
            }
        }
        
        return implode($separator, $arrValues);
    }
        
    public function toHtml()
    {
        
    }
    
}// Class Mage_Home_ContentBlock END