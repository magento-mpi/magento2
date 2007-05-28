<?php
/**
 * Admin messages controller
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_MessageController extends Mage_Core_Controller_Front_Action 
{
    /**
     * set JSON error message to response body
     *
     * @param   string $message
     * @return  Zend_Controller_Action
     */
    protected function jsonErrorAction()
    {
        $message = $this->getRequest()->getParam('message');
        $res = array(
            'error'         => 1,
            'message'       => $message,
            'errorMessage'  => $message
        );
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
}
