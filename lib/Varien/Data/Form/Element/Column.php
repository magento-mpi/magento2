<?php
/**
 * Form column
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Column extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->_initElementsCollection();
        $this->setType('column');
    }
}