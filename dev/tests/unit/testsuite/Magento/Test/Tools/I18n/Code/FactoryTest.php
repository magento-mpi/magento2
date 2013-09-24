<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\I18n\Code;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_factory = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Factory');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Writer for "filename.invalid_type" is not exist.
     */
    public function testGetContextByPathWithInvalidPath()
    {
        $this->_factory->createDictionaryWriter('filename.invalid_type');
    }
}
