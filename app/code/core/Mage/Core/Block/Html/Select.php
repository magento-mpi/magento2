<?php
/**
 * HTML select element block
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Block_Html_Select extends Mage_Core_Block_Abstract
{
    protected $_options = array();
    
    public function getOptions()
    {
        return $this->_options;
    }
    
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }
    
    public function addOption($value, $label, $params=array())
    {
        $this->_options[] = array('value'=>$value, 'label'=>$label);
        return $this;
    }
    
    public function setName($name)
    {
        $this->setData('name', $name);
        return $this;
    }
    
    public function setId($id)
    {
        $this->setData('id', $id);
        return $this;
    }
    
    public function setClass($class)
    {
        $this->setData('class', $class);
        return $this;
    }
    
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }
    
    public function getName()
    {
        return $this->getData('name');
    }
    
    public function getId()
    {
        return $this->getData('id');
    }
    
    public function getClass()
    {
        return $this->getData('class');
    }
    
    public function getTitle()
    {
        return $this->getData('title');
    }
    
    public function toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}

        $html = '<select name="'.$this->getName().'" id="'.$this->getId().'" class="'
            .$this->getClass().'" title="'.$this->getTitle().'" '.$this->getExtraParams().'>';
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }
        foreach ($this->getOptions() as $option) {
            $selected = in_array($option['value'], $value) ? ' selected' : '';
        	$html.= '<option value="'.$option['value'].'"'.$selected.'>'.$option['label'].'</option>';
        }
        $html.= '</select>';
        return $html;
    }
    
    public function getHtml()
    {
        return $this->toHtml();
    }
}
