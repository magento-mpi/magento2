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

abstract class Mage_Core_Block_Abstract
{
    /**
     * Info contains block id (db pk), name, parent, group
     *
     * @var array
     */
    protected $_info = array();
    
    /**
     * Attributes is whatever is being saved serialized in db
     *
     * @var array
     */
    protected $_attributes = array();
    
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
        $this->_attributes = $attributes;
    }

    /**
     * Get block data
     * 
     * @param     none
     * @return    array
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function getInfo($name='')
    {
        if (''===$name) {
            return $this->_info ;
        } elseif (isset($this->_info[$name])) {
            return $this->_info[$name];
        }
        return false;
    }
    
    /**
     * Set info variable value
     *
     * @param  string $info
     * @param  mixed  $value
     * @return Mage_Core_Block_Abstract
     */
    public function setInfo($info, $value='')
    {
        if (is_array($info) && ''===$value) {
            foreach ($info as $name=>$value) {
                $this->setInfo($name, $value);
            }
        } else {
          if (is_null($value)) {
              unset($this->_info[$info]);
          } else {
              $this->_info[$info] = $value;
          }
        }
        return $this;
    }
    
    /**
     * Get attribute value
     *
     * @param  string $name
     * @return mixed
     */
    public function getAttribute($name='')
    {
        if (''===$name) {
            return $this->_attributes ;
        } else {
            if (isset($this->_attributes[$name])) {
               return $this->_attributes[$name];
            }
        }
        return null;    
    }
    
    /**
     * Set data to _attributes array
     * 
     * @param     string $var_name
     * @return    Mage_Core_Block_Abstract
     */
    
    public function setAttribute($info, $value='')
    {
        if (is_array($info) && ''===$value) {
            foreach ($info as $name=>$value) {
                $this->setAttribute($name, $value);
            }
        } else {
            $this->_attributes[$info] = $value;
        }
        return $this;
    }

    /**
     * Set child block
     * 
     * @param     string $name
     * @param     mixed  $block
     * @return    Mage_Core_Block_Abstract
     */
    
    public function setChild($name, $block)
    {
        if (is_string($block)) {
            $block = Mage::getBlock($block);
            if (!$block) {
                Mage::exception('Invalid block name to set child '.$name.': '.$block);
            }
        }
        
        if ($block->getInfo('anonymous')) {
            
            $suffix = $block->getInfo('anonSuffix');
            if (empty($suffix)) {
                $suffix = 'child'.sizeof($this->_children);
            }
            $blockName = $this->getInfo('name').'.'.$suffix;

            Mage_Core_Block::unsetBlock($block->getInfo('name'));
            Mage_Core_Block::setBlock($blockName, $block);
            
            $block->setInfo('name', $blockName);
            $block->setInfo('anonymous', false);
            
            if (empty($name)) {
               $name = $blockName;
            }
        }
        
        $block->setInfo('parent', array('var'=>$name, 'block'=>$this));
        $this->_children[$name] = $block;

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
        if ($block->getInfo('anonymous')) {
            $this->setChild('', $block);        
            $name = $block->getInfo('name');
        } else {
            $name = $block->getInfo('name');
            $this->setChild($name, $block);        
        }
        
        $list = $this->getAttribute('sortedChildrenList');
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
        
        $this->setAttribute('sortedChildrenList', $list);
        
        return $this;
    }
    
    function append($block)
    {
        return $this->insert($block, '', true);
    }

    public function toArray()
    {
        $arr = array();
        
        $arr['attributes'] = $this->getAttribute();
        
        if ($parent = $this->getInfo('parent')) {
            $arr['parent'] = array('var'=>$parent['var'], 'block'=>$parent['block']->getInfo('name'));
        }

        return $arr;
    }
    
    public function loadFromArray($arr)
    {
        $this->_attributes = $arr['attributes'];

        if (!empty($arr['parent'])) {
            $arr['parent']['block'] = Mage::getBlock($arr['parent']['block']);
            $arr['parent']['block']->setChild($arr['parent']['var'], $this);
            $this->setInfo('parent', $arr['parent']);
        }
        
        return $this;
    }

    /**
     * Set required array elements
     *
     * @param   array $arr
     * @param   array $elements
     * @return  array
     */
    protected function _prepareArray(&$arr, $elements=array())
    {
        foreach ($elements as $element) {
            if (!isset($arr[$element])) {
                $arr[$element] = null;
            }
        }
        return $arr;
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
        
    public function toString()
    {
        
    }
    
}// Class Mage_Home_ContentBlock END