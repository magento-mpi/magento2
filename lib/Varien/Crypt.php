<?php

/**
 * Crypt factory
 *
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Crypt
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Crypt
{
    /**
     * Factory method to return requested cipher logic
     *
     * @param string $method
     * @return Varien_Crypt_Abstract
     */
    static public function factory($method='mcrypt')
    {
        $uc = str_replace(' ','_',ucwords(str_replace('_',' ',$method)));
        $className = 'Varien_Crypt_'.$uc;
        return new $className;
    }
}