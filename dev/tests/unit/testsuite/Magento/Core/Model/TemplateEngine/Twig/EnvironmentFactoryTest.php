<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Twig;

class EnvironmentFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_dirMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_extension;
    
    /** @var \PHPUnit_Framework_MockObject_MockObject \Magento\Core\Model\TemplateEngine\Twig\FullFileName */
    private $_loaderMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject \Magento\Filesystem */
    private $_filesystem;
    
    /** @var \PHPUnit_Framework_MockObject_MockObject \Magento\Core\Model\Logger */
    private $_loggerMock;
    
    /**
     * Validate \Twig_Environment returned on call
     */
    public function testCreatePositive()
    {
        $this->_filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnValue(null));
        
        $inst = new \Magento\Core\Model\TemplateEngine\Twig\EnvironmentFactory(
            $this->_filesystem,
            $this->_extension,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_loaderMock
        );
        /**
         * @var \Twig_Environment $factoryInst
         */
        $factoryInst = $inst->create();
        $this->assertInstanceOf('Twig_Environment', $factoryInst);
    }

    /**
     * Validate \Twig_Environment returned on call even though directory not created
     */
    public function testCreateNegative()
    {
        $this->_filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->throwException(new \Magento\Filesystem\FilesystemException()));
    
        $inst = new \Magento\Core\Model\TemplateEngine\Twig\EnvironmentFactory(
            $this->_filesystem,
            $this->_extension,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_loaderMock
        );
        /**
         * @var \Twig_Environment $factoryInst
        */
        $factoryInst = $inst->create();
        $this->assertInstanceOf('Twig_Environment', $factoryInst);
    }
    
    protected function setUp()
    {
        $this->_filesystem = $this->getMockBuilder('Magento\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
                
        $this->_dirMock = $this->getMockBuilder('Magento\Core\Model\Dir')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_loaderMock = $this->getMockBuilder('Magento\Core\Model\TemplateEngine\Twig\FullFileName')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_extensionFactory = $this->getMockBuilder('Magento\Core\Model\TemplateEngine\Twig\ExtensionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_extension = $this->getMockBuilder('Magento\Core\Model\TemplateEngine\Twig\Extension')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->_loggerMock = $this->getMockBuilder('Magento\Core\Model\Logger')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
