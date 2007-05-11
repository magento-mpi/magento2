<?php
/**
 * Form fieldset
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Fieldset extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('fieldset');
    }
}