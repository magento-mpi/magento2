<?php
/**
 * Test controller
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_TestController extends Mage_Core_Controller_Zend_Action 
{
    public function wizardAction()
    {
        $arr = array(
            'key' => 'value'
        );
        $this->getResponse()->setBody(Zend_Json::encode($arr));
    }
}
