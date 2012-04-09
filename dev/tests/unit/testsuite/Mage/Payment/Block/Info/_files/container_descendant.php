<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include 'method_descendant.php';

class Mage_Payment_Block_Info_Container_Descendant extends Mage_Payment_Block_Info_ContainerAbstract
{
    /**
     * @var Mage_Payment_Model_Info
     */
    protected $_paymentInfo;

    public function __construct()
    {
        parent::__construct();

        $this->_paymentInfo = new Mage_Payment_Model_Info;
        $method = new Mage_Payment_Model_Method_Descendant;
        $this->_paymentInfo->setMethodInstance($method);
    }

    public function getPaymentInfo()
    {
        return $this->_paymentInfo;
    }
}
