<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Theme\Helper\Storage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperStorage;

    /**
     * @var \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Content|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesContent;

    /**
     * @var \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    protected function setUp()
    {
        $this->_helperStorage = $this->getMock('Magento\Theme\Helper\Storage', array(), array(), '', false);
        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);
        $this->_request = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments =  $objectManagerHelper->getConstructArguments(
            'Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Content',
            array(
                'urlBuilder'    => $this->_urlBuilder,
                'request'       => $this->_request,
                'storageHelper'  => $this->_helperStorage,
            )
        );

        $this->_filesContent = $objectManagerHelper->getObject(
            'Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Content',
            $constructArguments
        );
    }

    /**
     * @dataProvider requestParamsProvider
     * @param array $requestParams
     */
    public function testGetNewFolderUrl($requestParams)
    {
        $expectedUrl = 'some_url';

        $this->_helperStorage->expects($this->once())
            ->method('getRequestParams')
            ->will($this->returnValue($requestParams));

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/newFolder', $requestParams)
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_filesContent->getNewfolderUrl());
    }

    /**
     * @dataProvider requestParamsProvider
     * @param array $requestParams
     */
    public function testGetDeleteFilesUrl($requestParams)
    {
        $expectedUrl = 'some_url';

        $this->_helperStorage->expects($this->once())
            ->method('getRequestParams')
            ->will($this->returnValue($requestParams));

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/deleteFiles', $requestParams)
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_filesContent->getDeleteFilesUrl());
    }

    /**
     * @dataProvider requestParamsProvider
     * @param array $requestParams
     */
    public function testGetOnInsertUrl($requestParams)
    {
        $expectedUrl = 'some_url';

        $this->_helperStorage->expects($this->once())
            ->method('getRequestParams')
            ->will($this->returnValue($requestParams));

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/onInsert', $requestParams)
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_filesContent->getOnInsertUrl());
    }

    /**
     * Data provider for requestParams
     * @return array
     */
    public function requestParamsProvider()
    {
        return array(
            'requestParams' => array(
                \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => 1,
                \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE,
                \Magento\Theme\Helper\Storage::PARAM_NODE         => 'root'
            )
        );
    }

    public function testGetTargetElementId()
    {
        $expectedRequest = 'some_request';

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with('target_element_id')
            ->will($this->returnValue($expectedRequest));

        $this->assertEquals($expectedRequest, $this->_filesContent->getTargetElementId());
    }

    public function testGetContentsUrl()
    {
        $expectedUrl = 'some_url';

        $expectedRequest = 'some_request';

        $requestParams = array(
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => 1,
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE,
            \Magento\Theme\Helper\Storage::PARAM_NODE         => 'root'
        );

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/contents', array('type' => $expectedRequest) + $requestParams)
            ->will($this->returnValue($expectedUrl));

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with('type')
            ->will($this->returnValue($expectedRequest));

        $this->_helperStorage->expects($this->once())
            ->method('getRequestParams')
            ->will($this->returnValue($requestParams));

        $this->assertEquals($expectedUrl, $this->_filesContent->getContentsUrl());
    }
}
