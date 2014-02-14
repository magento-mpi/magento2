<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Framework;

use Magento\TestFramework\Helper\ObjectManager;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Report\Framework\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->builder = $objectManagerHelper->getObject('Magento\Tools\Dependency\Report\Framework\Builder');
    }

    /**
     * @param array $options
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Passed option "config_files" is wrong.
     * @dataProvider dataProviderWrongOptionFilename
     */
    public function testBuildWithIfPassedFilename($options)
    {
        $this->builder->build($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionFilename()
    {
        return [
            [['files_for_parse' => [1, 2], 'report_filename' => 'filename']],
            [['files_for_parse' => [1, 2], 'report_filename' => 'filename', 'config_files' => []]],
        ];
    }
}
