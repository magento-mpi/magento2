<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Theme_Block_Adminhtml_Wysiwyg_Files_TreeTest extends PHPUnit_Framework_TestCase
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
     * @var \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Tree|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesTree;

    protected function setUp()
    {
        $this->_helperStorage = $this->getMock('Magento\Theme\Helper\Storage', array(), array(), '', false);
        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $constructArguments =  $objectManagerHelper->getConstructArguments(
            'Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content',
            array('urlBuilder'    => $this->_urlBuilder)
        );
        $this->_filesTree = $this->getMock(
            'Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Tree', array('helper'), $constructArguments
        );

        $this->_filesTree->expects($this->any())
            ->method('helper')
            ->with('Magento\Theme\Helper\Storage')
            ->will($this->returnValue($this->_helperStorage));
    }

    public function testGetTreeLoaderUrl()
    {
        $requestParams = array(
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => 1,
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE,
            \Magento\Theme\Helper\Storage::PARAM_NODE         => 'root'
        );
        $expectedUrl = 'some_url';

        $this->_helperStorage->expects($this->once())
            ->method('getRequestParams')
            ->will($this->returnValue($requestParams));

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/treeJson', $requestParams)
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_filesTree->getTreeLoaderUrl());
    }
}
