<?php

class Varien_Widget_Form_Element
{

    static public function factory($elementType, $config = array()) {

        if (!is_string($elementType) || empty($elementType)) {
            throw new Zend_Db_Exception('Adapter name must be specified in a string.');
        }

        $className = 'Varien_Widget_Form_Element_' . $elementType;
        Zend::loadClass($className);

        return  new $className($config);
    }
}