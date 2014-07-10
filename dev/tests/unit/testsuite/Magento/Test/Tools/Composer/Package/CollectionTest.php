<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Tools\Composer\Package;

use Magento\Tools\Composer\Package\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Composer\Package\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var Collection
     */
    private $model;

    protected function setUp()
    {
        $this->reader = $this->getMock('\Magento\Tools\Composer\Package\Reader', [], [], '', false);
        $this->model = new Collection($this->reader);
    }

    /**
     * @param string|bool $value
     * @param string $versionAgainst
     * @param string $expectedException
     * @dataProvider validateUpdateDependentDataProvider
     */
    public function testValidateUpdateDependent($value, $versionAgainst, $expectedException)
    {
        if ($expectedException) {
            $this->setExpectedException('\InvalidArgumentException', $expectedException);
        }
        Collection::validateUpdateDependent($value, $versionAgainst);
    }

    /**
     * @return array
     */
    public function validateUpdateDependentDataProvider()
    {
        $wildcard = Collection::DEPENDENCIES_WILDCARD;
        return [
            [Collection::DEPENDENCIES_EXACT, 'does not matter', false],
            [false, 'does not matter', false],
            ['anything', 'does not matter', "Unexpected value for 'dependent' argument: 'anything'"],
            [$wildcard, '1.2.3-beta.1', 'Wildcard may be set only fo stable versions (format: x.y.z)']
        ];
    }

    public function testReadPackages()
    {
        $pattern = 'sub/dir';
        $result = [
            'file/foo' => (object)['name' => 'foo'],
            'file/bar' => (object)['name' => 'bar'],
        ];
        $this->reader->expects($this->once())->method('readPattern')->with($pattern)->will($this->returnValue($result));
        $this->model->readPackages($pattern);
        $this->assertSame(['foo', 'bar'], $this->model->getPackageNames());
        $this->assertEquals($result['file/foo'], $this->model->getPackage('foo'));
        $this->assertNotSame($result['file/foo'], $this->model->getPackage('foo'));
        $this->assertEquals($result['file/bar'], $this->model->getPackage('bar'));
        $this->assertNotSame($result['file/bar'], $this->model->getPackage('bar'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No package name found in the file: something
     */
    public function testAddNoName()
    {
        $result = ['something' => (object)['not_a_name' => 'foo']];
        $this->reader->expects($this->once())->method('readPattern')->will($this->returnValue($result));
        $this->model->readPackages('...');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The package 'foo' was already read
     */
    public function testAddSameTwice()
    {
        $result = [
            'file/foo' => (object)['name' => 'foo'],
            'file/bar' => (object)['name' => 'foo'],
        ];
        $this->reader->expects($this->once())->method('readPattern')->will($this->returnValue($result));
        $this->model->readPackages('...');
    }

    public function testReadPackage()
    {
        $pattern = 'file/foo';
        $result = ['file/foo', (object)['name' => 'foo']];
        $this->reader->expects($this->once())->method('readOne')->with($pattern)->will($this->returnValue($result));
        $this->model->readPackage($pattern);
        $package = $this->model->getPackage('foo');
        $this->assertEquals($result[1], $package);
        $this->assertNotSame($result[1], $package);
    }

    /**
     * @param string|bool $updateDependent
     * @param string $expectedBarFoo
     * @param string $expectedBazFoo
     * @param string $expectedBazBar
     * @dataProvider setVersionDataProvider
     */
    public function testSetVersion($updateDependent, $expectedBarFoo, $expectedBazFoo, $expectedBazBar)
    {
        $result = [
            'foo' => json_decode('{"name":"foo","version":"1.0.0"}'),
            'bar' => json_decode('{"name":"bar","version":"1.0.0","require":{"foo":"1.0.0"}}'),
            'baz' => json_decode('{"name":"baz","version":"1.0.0","replace":{"foo":"1.0.0","bar":"1.0.0"}}'),
        ];
        $this->reader->expects($this->once())->method('readPattern')->will($this->returnValue($result));
        $this->model->readPackages('...');
        $foo = $this->model->getPackage('foo');
        $bar = $this->model->getPackage('bar');
        $baz = $this->model->getPackage('baz');
        $this->assertEquals('1.0.0', $foo->version);
        $this->assertEquals('1.0.0', $bar->version);
        $this->assertEquals('1.0.0', $bar->require->foo);
        $this->assertEquals('1.0.0', $baz->version);
        $this->assertEquals('1.0.0', $baz->replace->foo);
        $this->assertEquals('1.0.0', $baz->replace->bar);
        $this->assertSame([], $this->model->getModified());
        $this->model->setVersion('foo', '2.0.0', $updateDependent);
        $this->assertEquals('2.0.0', $foo->version);
        $this->assertEquals($expectedBarFoo, $bar->require->foo);
        $this->assertEquals($expectedBazFoo, $baz->replace->foo);
        $this->assertEquals($expectedBazBar, $baz->replace->bar);
        if ($updateDependent) {
            $this->assertSame(['foo' => $foo, 'bar' => $bar, 'baz' => $baz], $this->model->getModified());
        } else {
            $this->assertSame(['foo' => $foo], $this->model->getModified());
        }
    }

    /**
     * @return array
     */
    public function setVersionDataProvider()
    {
        return [
            [false, '1.0.0', '1.0.0', '1.0.0'],
            [Collection::DEPENDENCIES_EXACT, '2.0.0', '2.0.0', '1.0.0'],
            [Collection::DEPENDENCIES_WILDCARD, '2.0.*', '2.0.*', '1.0.0'],
        ];
    }
}
