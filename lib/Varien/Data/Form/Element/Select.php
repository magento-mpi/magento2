<?php
/**
 * Form select element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Select extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
    }
}