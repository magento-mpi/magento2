<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\View\Page\Config
 */
namespace Magento\Framework\View\Page;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Page\Title
     */
    protected $title;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    public function setUp()
    {
        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->title = new Title($this->scopeConfig);
    }

    public function testSet()
    {
        $value = 'test_value';
        $this->title->set($value);
        $this->assertEquals(' test_value ', $this->title->get());
    }

    public function testUnset()
    {
        $value = 'test';
        $this->title->set($value);
        $this->assertEquals(' test ', $this->title->get());
        $this->title->unsetValue();
        $this->assertEquals('  ', $this->title->get());
    }

    public function testGet()
    {
        $this->scopeConfig->expects($this->exactly(2))->method('getValue');
        $this->title->set('test');
        $this->assertEquals(' test ', $this->title->get());
    }

    public function testGetShort()
    {
        $settingMap = [
            ['design/head/title_prefix', 'prefix'],
            ['design/head/title_suffix', 'suffix']
        ];
        $this->scopeConfig->expects($this->any())->method('getValue')->will($this->returnValueMap($settingMap));
        $value = 'test';
        $this->title->set($value);
        $this->title->get();
    }
//
//    public function testGetAsStringArray()
//    {
//        $value = ['test', 'test2'];
//        $this->title->set($value);
//        $this->assertEquals('test / test2', $this->title->get());
//    }
}
