<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleOptimizer Front Controller
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @name       Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve js scripts by parsing remote Google Optimizer page
     */
    public function codesAction()
    {
        if ($this->getRequest()->getQuery('url')) {
            $client = new Varien_Http_Client($this->getRequest()->getQuery('url'));
            $response = $client->request(Varien_Http_Client::GET);
            $result = array();
            if (preg_match_all('/<textarea[^>]*id="([_a-zA-Z0-9]+)"[^>]*>([^<]+)<\/textarea>/', $response->getRawBody(), $matches)) {
                $c = count($matches[1]);
                for ($i = 0; $i < $c; $i++) {
                    $id = $matches[1][$i];
                    $code = $matches[2][$i];
                    $result[$id] = $code;
                }
            }
            $this->getResponse()->setBody( Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result) );
        }
    }
}
