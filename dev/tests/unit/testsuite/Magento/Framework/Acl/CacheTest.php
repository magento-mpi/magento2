<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Acl;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $model;

    /**
     * @var \Magento\Framework\Config\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheConfig;

    protected $cacheKey;

    protected function setUp()
    {
        $this->cacheConfig = $this->getMock('Magento\Framework\Config\CacheInterface');
        $this->cacheKey = 'test_key';

        $this->model = new Cache($this->cacheConfig, $this->cacheKey);
    }

    /**
     * @param $dataAcl
     * @dataProvider aclDataProvider
     */
    public function testGet($dataAcl)
    {
        $this->initAcl((is_array($dataAcl) ? serialize($dataAcl): $dataAcl));
        $this->assertEquals($dataAcl, $this->model->get());
    }

    /**
     * @return array
     */
    public function aclDataProvider()
    {
        return [
            ['dataAcl' => ['someKey' => 'someValue']],
            ['dataAcl' => false]
        ];
    }

    /**
     * @param $expectedTest
     * @dataProvider hasWithoutAclDataProvider
     */
    public function testHasWithoutAcl($expectedTest)
    {
        $this->cacheConfig->expects($this->once())->method('test')->will($this->returnValue($expectedTest));
        $this->assertEquals($expectedTest, $this->model->has());
    }

    /**
     * @return array
     */
    public function hasWithoutAclDataProvider()
    {
        return [
            ['expectedTest' => true],
            ['expectedTest' => false]
        ];
    }

    /**
     * @param $dataAcl
     * @dataProvider aclDataProvider
     */
    public function testHasWithAcl($dataAcl)
    {
        $this->initAcl((is_array($dataAcl) ? serialize($dataAcl): $dataAcl));
        $this->cacheConfig->expects($this->never())->method('test');

        $this->model->get();
        $this->assertTrue($this->model->has());
    }

    protected function initAcl($aclData)
    {
        $this->cacheConfig->expects($this->once())
            ->method('load')
            ->with($this->cacheKey)
            ->will($this->returnValue($aclData));
    }

    public function testSave()
    {
        $acl = $this->getMockBuilder('Magento\Framework\Acl')->disableOriginalConstructor()->getMock();

        $this->cacheConfig->expects($this->once())->method('save')->with(serialize($acl), $this->cacheKey);
        $this->model->save($acl);
    }

    public function testClean()
    {
        $this->cacheConfig->expects($this->once())
            ->method('remove')
            ->with($this->cacheKey);
        $this->model->clean();
    }
}
