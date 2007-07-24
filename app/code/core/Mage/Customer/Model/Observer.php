<?php
/**
 * Customer module observer
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Moshe Gurvich <moshe@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Customer_Model_Observer
{
    public function beforeGenerateLayoutBlocks($observer)
    {
    	$layout = $observer->getEvent()->getLayout();
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$layout->loadUpdateFile(
    			Mage::getDesign()->getLayoutFilename('customer/loggedIn.xml')
    		);
    	} else  {
    		$layout->loadUpdateFile(
    			Mage::getDesign()->getLayoutFilename('customer/loggedOut.xml')
    		);

    	}
    }
}
