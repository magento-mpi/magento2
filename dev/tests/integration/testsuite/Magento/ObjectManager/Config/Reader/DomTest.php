<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ObjectManager\Config\Reader;

class DomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\Config\Reader\Dom
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var \Magento\App\Arguments\FileResolver\Primary
     */
    protected $_fileResolverMock;

    /**
     * @var \DOMDocument
     */
    protected $_mergedConfig;

    /**
     * @var \Magento\App\Arguments\ValidationState
     */
    protected $_validationState;

    /**
     * @var \Magento\ObjectManager\Config\SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var \Magento\ObjectManager\Config\Mapper\Dom
     */
    protected $_mapper;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/../../_files') . '/';
        $this->_fileList = array(
            file_get_contents($fixturePath . 'config_one.xml'),
            file_get_contents($fixturePath . 'config_two.xml'),
        );

        $this->_fileResolverMock = $this->getMock(
            'Magento\App\Arguments\FileResolver\Primary', array(), array(), '', false
        );
        $this->_fileResolverMock->expects($this->once())->method('get')->will($this->returnValue($this->_fileList));
        $this->_mapper = new \Magento\ObjectManager\Config\Mapper\Dom();
        $this->_validationState =
            new \Magento\App\Arguments\ValidationState(\Magento\App\State::MODE_DEFAULT);
        $this->_schemaLocator = new \Magento\ObjectManager\Config\SchemaLocator();

        $this->_mergedConfig = new \DOMDocument();
        $this->_mergedConfig->load($fixturePath . 'config_merged.xml');
    }

    public function testRead()
    {
        $model = new \Magento\ObjectManager\Config\Reader\Dom(
            $this->_fileResolverMock,
            $this->_mapper,
            $this->_schemaLocator,
            $this->_validationState
        );
        $this->assertEquals($this->_mapper->convert($this->_mergedConfig), $model->read('scope'));
    }

}
