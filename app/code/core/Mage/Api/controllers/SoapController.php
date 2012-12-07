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
class Mage_Api_SoapController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        /* @var $server Mage_Api_Model_Server */
        $this->_getServer()->init($this, 'soap')
            ->run();
    }
} // Class Mage_Api_IndexController End
