<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Pci\Model\Encryption'
        );
    }

    public function testEncryptDecrypt()
    {
        $this->assertEquals('', $this->_model->decrypt($this->_model->encrypt('')));
        $this->assertEquals('test', $this->_model->decrypt($this->_model->encrypt('test')));
    }
}
