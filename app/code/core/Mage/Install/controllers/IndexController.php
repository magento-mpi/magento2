<?php
/**
 * Install index controller
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Install_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction() 
    {
        $this->_forward('begin', 'wizard', 'Mage_Install');
    }
}// Class IndexController END