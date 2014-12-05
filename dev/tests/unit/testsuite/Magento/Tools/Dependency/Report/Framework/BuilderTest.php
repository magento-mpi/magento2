<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Framework;

use Magento\TestFramework\Helper\ObjectManager;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Report\Framework\Builder
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
     * @expectedExceptionMessage Parse error. Passed option "config_files" is wrong.
     * @dataProvider dataProviderWrongOptionConfigFiles
     */
    public function testBuildWithWrongOptionConfigFiles($options)
    {
        $this->builder->build($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionConfigFiles()
    {
        return array(
            array(
                array(
                    'parse' => array('files_for_parse' => array(1, 2), 'config_files' => array()),
                    'write' => array(1, 2)
                )
            ),
            array(array('parse' => array('files_for_parse' => array(1, 2)), 'write' => array(1, 2)))
        );
    }
}
