<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice apia2 REST route
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Route_Rest extends Mage_Api2_Model_Route_Abstract implements Mage_Api2_Model_Route_Interface
{
    /**
     * Retrieve active controller
     *
     * // TODO: Change return class to API generic action controller
     * @return Mage_Core_Controller_Varien_Action
     */
    public function getController()
    {
        // TODO: Implement
        require Mage::getBaseDir('app') . "/code/core/Mage/Customer/controllers/Rest/IndexController.php";
        return new Mage_Customer_Rest_IndexController(Mage::app()->getRequest(), Mage::app()->getResponse());
    }
}
