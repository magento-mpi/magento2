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
        $this->_construct();
    }
    
    protected function _construct()
    {
        
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
            $block = $this->getLayout()->getBlock($block);
            if (!$block) {
                Mage::exception('Invalid block name to set child '.$name.': '.$block);
            }
        }
        
        if ($block->getIsAnonymous()) {
            
            $suffix = $block->getAnonSuffix();
            if (empty($suffix)) {
                $suffix = 'child'.sizeof($this->_children);
            }
            $blockName = $this->getName().'.'.$suffix;

            $this->getLayout()->unsetBlock($block->getName());
            $this->getLayout()->setBlock($blockName, $block);
            
            $block->setName($blockName);
            $block->setIsAnonymous(false);
            
            if (empty($name)) {
               $name = $blockName;
            }
        }
        
        $block->setParent(array('var'=>$name, 'block'=>$this));
        $this->_children[$name] = $block;

        return $this;
    }
    
    public function unsetChild($name)
    {
        unset($this->_children[$name]);
        $list = $this->getSortedChildrenList();
        $key = array_search($name, $list);
        if (!empty($key)) {
            unset($list[$key]);
            $this->setSortedChildrenList($list);
        }
    }
    
    public function unsetChildren()
    {
        $this->_children = array();
        $this->setSortedChildrenList(array());
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
        if ($block->getIsAnonymous()) {
            $this->setChild('', $block);        
            $name = $block->getName();
        } else {
            $name = $block->getName();
            $this->setChild($name, $block);        
        }
        
        $list = $this->getSortedChildrenList();
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
        
        $this->setSortedChildrenList($list);
        
        return $this;
    }
    
    function append($block)
    {
        return $this->insert($block, '', true);
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