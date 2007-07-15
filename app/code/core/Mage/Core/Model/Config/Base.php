<?php

/**
 * Abstract configuration class
 *
 * Used to retrieve core configuration values
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Core
 * @link        http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Core_Model_Config_Base extends Varien_Simplexml_Config
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_elementClass = 'Mage_Core_Model_Config_Element';
        parent::__construct();
    }
}