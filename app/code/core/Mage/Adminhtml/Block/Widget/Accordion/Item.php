<?php
/**
 * Accordion item
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Accordion_Item extends Mage_Adminhtml_Block_Widget
{
    protected $_accordion;
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function setAccordion($accordion)
    {
        $this->_accordion = $accordion;
        return $this;
    }
    
    public function getTarget()
    {
        return ($this->getAjax()) ? 'ajax' : '';
    }
    
    public function getTitle()
    {
        $title  = $this->getData('title');
        $url    = $this->getContentUrl() ? $this->getContentUrl() : '#';
        $title  = '<a href="'.$url.'" target="'.$this->getTarget().'">'.$title.'</a>';

        return $title;
    }
    
    public function getContent()
    {
        $content = $this->getData('content');
        if (is_string($content)) {
            return $content;
        }
        if ($content instanceof Mage_Core_Block_Abstract) {
            return $content->toHtml();
        }
        return null;
    }
    
    public function getClass()
    {
        $class = $this->getData('class');
        if ($this->getOpen()) {
            $class.= ' open';
        }
        return $class;
    }
    
    public function toHtml()
    {
        $html = '<dt id="dt-'.$this->getHtmlId().'" class="'.$this->getClass().'">';
        $html.= $this->getTitle();
        $html.= '</dt>';
        $html.= '<dd id="dd-'.$this->getHtmlId().'" class="'.$this->getClass().'">';
        $html.= $this->getContent();
        $html.= '</dd>';
        return $html;
    }
}
