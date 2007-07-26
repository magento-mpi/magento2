<?php
/**
 * Button widget
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getType()
    {
        return $type=$this->getData('type') ? $type : 'button';
    }

    public function getOnClick()
    {
        if (!$this->getData('on_click')) {
            return $this->getData('onclick');
        }
        return $this->getData('on_click');
    }

    public function toHtml()
    {
        $html = '<button id="'.$this->getId().'" name="'.$this->getName().'" type="'.$this->getType().'" class="scalable '.$this->getClass().'" onclick="'.$this->getOnClick().'">';
        $html.= '<table cellspacing="0"><tr><td class="tl"></td><td class="tr"></td></tr>';
        $html.= '<tr><td class="ml"><span>'.$this->getLabel().'</span></td><td class="mr"></td></tr>';
        $html.= '<tr><td class="bl"></td><td class="br"></td></tr></table></button>';

        return $html;
    }
}
