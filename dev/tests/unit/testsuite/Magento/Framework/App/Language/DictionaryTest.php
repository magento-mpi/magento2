<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\Language;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Language\Dictionary
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dir;

    protected function setUp()
    {
        $this->dir = $this->getMockForAbstractClass('\Magento\Framework\Filesystem\Directory\ReadInterface');
        $filesystem = $this->getMock('\Magento\Framework\App\Filesystem', [], [], '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Framework\App\Filesystem::LOCALE_DIR)
            ->will($this->returnValue($this->dir))
        ;
        $this->model = new Dictionary($filesystem);
    }

    public function testGetDictionary()
    {
        $dir = [
            'Foo/en_AU/language.xml',
            'Bar/en_GB/language.xml',
            'Baz/en_GB/language.xml',
            'Bar/en_US/language.xml'
        ];
        $xmlMap = [
            [
                $dir[0],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>en_AU</code>
                    <vendor>Foo</vendor>
                    <use vendor="Bar" code="en_GB"/>
                    <use vendor="Baz" code="en_GB"/>
                </language>'
            ],
            [
                $dir[1],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>en_GB</code>
                    <vendor>Bar</vendor>
                    <sort_order>100</sort_order>
                    <use vendor="Bar" code="en_US"/>
                </language>'
            ],
            [
                $dir[2],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>en_GB</code>
                    <vendor>Baz</vendor>
                    <sort_order>50</sort_order>
                </language>'
            ],
            [
                $dir[3],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>en_US</code>
                    <vendor>Bar</vendor>
                </language>'
            ],
        ];
        $csvMap = [
            ['Bar/en_US/*.csv', null, ['Bar/en_US/b.csv', 'Bar/en_US/a.csv']],
            ['Baz/en_GB/*.csv', null, ['Baz/en_GB/1.csv']],
            ['Bar/en_GB/*.csv', null, ['Bar/en_GB/1.csv']],
            ['Foo/en_AU/*.csv', null, ['Foo/en_AU/1.csv', 'Foo/en_AU/2.csv']],
        ];
        $dictionaryMap = [
            ['Bar/en_US/a.csv', $this->getCsvMock([['one', '1'], ['two', '2']])],
            ['Bar/en_US/b.csv', $this->getCsvMock([['three', '3'], ['four', '4']])],
            ['Baz/en_GB/1.csv', $this->getCsvMock([['four and 5/10', '4.5']])],
            ['Bar/en_GB/1.csv', $this->getCsvMock([['four and 75/100', '4.75'], ['four and 5/10', '4.50']])],
            ['Foo/en_AU/1.csv', $this->getCsvMock([['one', '1.0'], ['five', '5.0']])],
            ['Foo/en_AU/2.csv', $this->getCsvMock([['six', '6.0']])],
        ];
        $this->dir->expects($this->any())->method('search')->will($this->returnValueMap(
            array_merge([['*/*/language.xml', null, $dir]], $csvMap)
        ));
        $this->dir->expects($this->any())->method('readFile')->will($this->returnValueMap($xmlMap));
        $this->dir->expects($this->any())->method('openFile')->will($this->returnValueMap($dictionaryMap));
        $result = $this->model->getDictionary('en_AU');
        $this->assertSame(
            [
                'one' => '1.0',
                'two' => '2',
                'three' => '3',
                'four' => '4',
                'four and 5/10' => '4.50',
                'four and 75/100' => '4.75',
                'five' => '5.0',
                'six' => '6.0'
            ],
            $result
        );
    }

    /**
     * Imitate a CSV-file read operation through "App filesystem" interface
     *
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCsvMock($data)
    {
        $file = $this->getMockForAbstractClass('Magento\Framework\Filesystem\File\ReadInterface');
        for ($i = 0; $i < count($data); $i++) {
            $file->expects($this->at($i))->method('readCsv')->will($this->returnValue($data[$i]));
        }
        $file->expects($this->at($i))->method('readCsv')->will($this->returnValue(false));
        return $file;
    }

    /**
     * @expectedException \LogicException
     */
    public function testCircularDependency()
    {
        $dir = [
            'Vendor1/uk_UA/language.xml',
            'Vendor2/uk_UB/language.xml',
            'Vendor3/uk_UC/language.xml',
        ];
        $xmlMap = [
            [
                $dir[0],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>uk_UA</code>
                    <vendor>Vendor1</vendor>
                    <use vendor="Vendor2" code="uk_UB"/>
                    <use vendor="Vendor3" code="uk_UC"/>
                </language>'
            ],
            [
                $dir[1],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>uk_UB</code>
                    <vendor>Vendor2</vendor>
                    <use vendor="Vendor3" code="uk_UC"/>
                </language>'
            ],
            [
                $dir[2],
                null,
                null,
                '<?xml version="1.0"?>
                <language>
                    <code>uk_UC</code>
                    <vendor>Vendor3</vendor>
                    <use vendor="Vendor1" code="uk_UA"/>
                </language>'
            ],
        ];
        $csvMap = [
            ['Vendor1/uk_UA/*.csv', null, ['Vendor1/uk_UA/1.csv']],
            ['Vendor2/uk_UB/*.csv', null, ['Vendor2/uk_UB/2.csv']],
            ['Vendor3/uk_UC/*.csv', null, ['Vendor3/uk_UC/3.csv']],
        ];
        $this->dir->expects($this->any())->method('search')->will($this->returnValueMap(
            array_merge([['*/*/language.xml', null, $dir]], $csvMap)
        ));
        $this->dir->expects($this->any())->method('readFile')->will($this->returnValueMap($xmlMap));
        $this->model->getDictionary('uk_UA');
    }
}
