<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\View\Layout\Reader\Block
 */
namespace Magento\Framework\View\Layout\Reader;


class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Layout\Reader\Block
     */
    protected $model;

    /**
     * @var \Magento\Framework\View\Layout\Reader\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\View\Layout\Element
     */
    protected $element;

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Layout\Argument\Parser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parser;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeResolver;

    /**
     * @var \Magento\Framework\View\Layout\Reader\Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure\Helper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = $this->getMockBuilder('Magento\Framework\View\Layout\Argument\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')->getMock();
        $this->scopeResolver = $this->getMockForAbstractClass(
            'Magento\Framework\App\ScopeResolverInterface',
            [],
            '',
            false
        );

        $this->pool = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Pool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scheduledStructure = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure')
            ->disableOriginalConstructor()->setMethods(['setElementToRemoveList', '__wakeup'])
            ->getMock();

        $this->model = new \Magento\Framework\View\Layout\Reader\Block(
            $this->helper,
            $this->parser,
            $this->scopeConfig,
            $this->scopeResolver,
            $this->pool
        );
    }

    public function testGetSupportedNodes()
    {
        $expected = ['block', 'referenceBlock'];
        $this->assertEquals($expected, $this->model->getSupportedNodes());
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($xml)
    {
        $this->context = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->element = new \Magento\Framework\View\Layout\Element($xml);

        $this->context->expects($this->any())->method('getScheduledStructure')->will(
            $this->returnValue($this->scheduledStructure)
        );
        $this->model->process($this->context, $this->element, $this->element);
    }

    public function processDataProvider()
    {
        return [
            [
                $xml = '<?xml version="1.0"?>
<page>
    <body>
        <referenceBlock name="head">
            <block class="Magento\Theme\Block\Html\Head\Css" name="jquery-fileuploader-css-jquery-fileupload-ui-css">
                <arguments>
                    <argument name="file">jquery/fileUploader/css/jquery.fileupload-ui.css</argument>
                </arguments>
            </block>
            <block class="Magento\Theme\Block\Html\Head\Script" name="jquery-fileuploader-bootstrap-js">
                <arguments>
                    <argument name="file">jquery/fileUploader/bootstrap.js</argument>
                </arguments>
            </block>
        </referenceBlock>
    </body>
</page>'
            ],
            [
                $xml = '<?xml version="1.0"?>
        <referenceBlock name="head">
            <block class="Magento\Theme\Block\Html\Head\Css" name="jquery-fileuploader-css-jquery-fileupload-ui-css">
                <arguments>
                    <argument name="file">jquery/fileUploader/css/jquery.fileupload-ui.css</argument>
                </arguments>
            </block>
            <block class="Magento\Theme\Block\Html\Head\Script" name="jquery-fileuploader-bootstrap-js">
                <arguments>
                    <argument name="file">jquery/fileUploader/bootstrap.js</argument>
                </arguments>
            </block>
        </referenceBlock>'
            ],
            [
                $xml = '<?xml version="1.0"?>
            <block class="Magento\Theme\Block\Html\Head\Css" name="jquery-fileuploader-css-jquery-fileupload-ui-css">
                <arguments>
                    <argument name="file">jquery/fileUploader/css/jquery.fileupload-ui.css</argument>
                </arguments>
            </block>'
            ],
            [
                $xml = '<?xml version="1.0"?>
        <referenceBlock name="head">
            <action method="addTab">
                <argument name="name">properties_section</argument>
                <argument name="block">banner_edit_tab_properties</argument>
            </action>
        </referenceBlock>'
            ]
        ];
    }
}
