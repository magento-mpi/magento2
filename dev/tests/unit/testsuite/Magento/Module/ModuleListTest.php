<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

class ModuleListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;


    protected function setUp()
    {
        $this->cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->readerMock = $this->getMock('Magento\Module\Declaration\Reader\Filesystem', array(), array(), '', false);
    }

    public function testGetModulesWhenDataIsCached()
    {
        $data = array(
            'declared_module' => array(
                'name' => 'declared_module',
                'version' => '1.0.0.0',
                'active' => false,
            ),
        );
        $cacheId = 'global::modules_declaration_cache';
        $this->cacheMock->expects($this->once())->method('load')->with($cacheId)->will($this->returnValue(
            serialize($data)
        ));
        $this->readerMock->expects($this->never())->method('read');
        $this->cacheMock->expects($this->never())->method('save');
        $model = new ModuleList(
            $this->readerMock,
            $this->cacheMock
        );
        $this->assertEquals($data, $model->getModules());
    }

    public function testGetModuleWhenDataIsNotCached()
    {
        $moduleData = array(
            'name' => 'declared_module',
            'version' => '1.0.0.0',
            'active' => false,
        );
        $data = array(
            'declared_module' => $moduleData,
        );
        $cacheId = 'global::modules_declaration_cache';
        $this->cacheMock->expects($this->once())->method('load')->with($cacheId);
        $this->readerMock->expects($this->once())->method('read')->with('global')->will($this->returnValue($data));
        $this->cacheMock->expects($this->once())->method('save')->with(serialize($data), $cacheId);
        $model = new ModuleList(
            $this->readerMock,
            $this->cacheMock
        );
        $this->assertEquals($moduleData, $model->getModule('declared_module'));
        $this->assertNull($model->getModule('not_declared_module'));
    }

}
