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

namespace Magento\Pci\Model;

class EncryptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pci\Model\Encryption
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Pci\Model\Encryption');
    }

    public function testEncryptDecrypt()
    {
        $this->assertEquals('', $this->_model->decrypt($this->_model->encrypt('')));
        $this->assertEquals('test', $this->_model->decrypt($this->_model->encrypt('test')));
    }
}
