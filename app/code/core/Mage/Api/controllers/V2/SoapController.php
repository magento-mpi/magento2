<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservice main controller
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_V2_SoapController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        if(Mage::helper('Mage_Api_Helper_Data')->isComplianceWSI()){
            $handler_name = 'soap_wsi';
        } else {
            $handler_name = 'soap_v2';
        }

        /* @var $server Mage_Api_Model_Server */
        $this->_getServer()->init($this, $handler_name, $handler_name)
            ->run();
    }
} // Class Mage_Api_IndexController End
