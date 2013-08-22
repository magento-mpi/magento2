<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Pci_Model_EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Pci_Model_Encryption
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Pci_Model_Encryption');
    }

    public function testEncryptDecrypt()
    {
        $this->assertEquals('', $this->_model->decrypt($this->_model->encrypt('')));
        $this->assertEquals('test', $this->_model->decrypt($this->_model->encrypt('test')));
    }
}
