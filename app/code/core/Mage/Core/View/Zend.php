<?php

/**
 * Abstract master class for extension.
 */
#require_once 'Zend/View/Abstract.php';


/**
 * Concrete class for handling view scripts.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mage_Core_View_Zend extends Zend_View_Abstract
{
    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        #Mage::setTimer(__METHOD__);
        include func_get_arg(0);
        #Mage::setTimer(__METHOD__, true);
    }
}
