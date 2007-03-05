<?php

/**
 * Form element factory
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Core_Block_Form_Element
{

    static public function factory($elementType, $attributes = array()) {

        if (!is_string($elementType) || empty($elementType)) {
            throw new Zend_Db_Exception('Adapter name must be specified in a string.');
        }
        
        $elementType = ucfirst(strtolower($elementType));
        $module = isset($attriutes['module']) ? ucfirst(strtolower($attributes['module'])) : 'Core';
        
        $className = 'Ecom_'.$module.'_Block_Form_Element_' . $elementType;

        return new $className($attributes);
    }
}