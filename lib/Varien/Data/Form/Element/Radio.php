<?php
/**
 * Form radio element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Radio extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $radios = $this->getRadios();
        foreach( $radios as $key => $radio ) {
            if( is_array($radio) ) {
                $tmp_var = new Varien_Object();
                $tmp_var->setHtmlId($key);
                $radios[] = $tmp_var->addData($radio);
                unset($radios[$key]);
            }
        }

        $this->setRadios($radios);
        $this->setType('radio');
        $this->setExtType('radio');
    }
}