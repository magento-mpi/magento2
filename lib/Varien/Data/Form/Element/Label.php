<?php
/**
 * Data form abstract class
 *
 * @package    Varien
 * @subpackage Form
 * @author     Ivan Chepurnyi <ivan@varien.com>
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Label extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('label');        
    }
    
    public function getElementHtml() 
    {
    	$html = $this->getBold() ? '<strong>' : '';
    	$html.= $this->getEscapedValue();
    	$html.= $this->getBold() ? '</strong>' : '';
    	return $html;
    }
	
}