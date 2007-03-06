<?php



/**
 * Tree Controller
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Andrey Korolyov <andrey@varien.com>
 * @date       Wed Feb 07 04:25:14 EET 2007
 */

class Mage_Customer_TreeController extends Mage_Core_Controller_Admin_Action
{
    function indexAction() 
    {
        $this->getResponse()->setBody($this->_view->render('customer/tree.php'));
    }
    
    function recentCustomersAction()
    {
        $json = array();
        $json[] = array('text'=>'Customer #123', 'id'=>'recent-customer-123', 'cls'=>'customer', 'leaf'=>'true');
        $json[] = array('text'=>'Customer #125', 'id'=>'recent-customer-125', 'cls'=>'customer', 'leaf'=>'true');
        $this->getResponse()->setBody(json_encode($json));
    }
    
    function recentSearchesAction()
    {
        
    }
    
    function savedSearchesAction()
    {
        
    }

}// Class IndexController END