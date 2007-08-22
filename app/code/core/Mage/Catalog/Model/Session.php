<?php
/**
 * Catalog session model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct() 
    {
        $this->init('catalog');
    }
}
