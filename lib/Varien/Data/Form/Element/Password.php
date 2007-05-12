<?php
/**
 * Form password element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Password extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('password');
        $this->setExtType('textfield');
    }
}