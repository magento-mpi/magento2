<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_dirMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_extension;
    
    /** @var PHPUnit_Framework_MockObject_MockObject Magento_Core_Model_TemplateEngine_Twig_FullFileName */
    private $_loaderMock;

    /** @var PHPUnit_Framework_MockObject_MockObject Magento_Filesystem */
    private $_filesystem;
    
    /** @var PHPUnit_Framework_MockObject_MockObject Magento_Core_Model_Logger */
    private $_loggerMock;
    
    /**
     * Validate Twig_Environment returned on call
     */
    public function testCreatePositive()
    {
        $this->_filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnValue(null));
        
        $inst = new Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactory(
            $this->_filesystem,
            $this->_extension,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_loaderMock
        );
        /**
         * @var Twig_Environment $factoryInst
         */
        $factoryInst = $inst->create();
        $this->assertInstanceOf('Twig_Environment', $factoryInst);
    }

    /**
     * Validate Twig_Environment returned on call even though directory not created
     */
    public function testCreateNegative()
    {
        $this->_filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->throwException(new Magento_Filesystem_Exception()));
    
        $inst = new Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactory(
            $this->_filesystem,
            $this->_extension,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_loaderMock
        );
        /**
         * @var Twig_Environment $factoryInst
        */
        $factoryInst = $inst->create();
        $this->assertInstanceOf('Twig_Environment', $factoryInst);
    }
    
    protected function setUp()
    {
        $this->_filesystem = $this->getMockBuilder('Magento_Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
                
        $this->_dirMock = $this->getMockBuilder('Magento_Core_Model_Dir')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_loaderMock = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_Twig_FullFileName')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_extensionFactory = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_Twig_ExtensionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_extension = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_Twig_Extension')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->_loggerMock = $this->getMockBuilder('Magento_Core_Model_Logger')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
