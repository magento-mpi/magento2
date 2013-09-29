<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Email\Template\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Email\Template\Config\SchemaLocator
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    protected function setUp()
    {
        $this->_moduleReader = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Core')
            ->will($this->returnValue('fixture_dir'))
        ;
        $this->_model = new \Magento\Core\Model\Email\Template\Config\SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $actualResult = $this->_model->getSchema();
        $this->assertEquals('fixture_dir/email_templates.xsd', $actualResult);
        // Makes sure the value is calculated only once
        $this->assertEquals($actualResult, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $actualResult = $this->_model->getPerFileSchema();
        $this->assertEquals('fixture_dir/email_templates.xsd', $actualResult);
        // Makes sure the value is calculated only once
        $this->assertEquals($actualResult, $this->_model->getPerFileSchema());
    }
}
