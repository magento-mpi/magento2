<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Dependency;

class DbRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbRule
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new DbRule(array('some_table' => 'SomeModule'));
    }

    /**
     * @param string $module
     * @param string $file
     * @param string $contents
     * @param array $expected
     * @dataProvider getDependencyInfoDataProvider
     */
    public function testGetDependencyInfo($module, $file, $contents, array $expected)
    {
        $this->assertEquals($expected, $this->model->getDependencyInfo($module, 'any', $file, $contents));
    }

    public function getDependencyInfoDataProvider()
    {
        return array(
            array('any', 'non-resource-file-path.php', 'any', array()),
            array(
                'any',
                '/app/some/path/sql/some-file.php',
                '$install->getTableName("unknown_table")',
                array(array('module' => 'Unknown', 'source' => 'unknown_table'))
            ),
            array(
                'any',
                '/app/some/path/data/some-file.php',
                '$install->getTableName("unknown_table")',
                array(array('module' => 'Unknown', 'source' => 'unknown_table'))
            ),
            array(
                'SomeModule',
                '/app/some/path/resource/some-file.php',
                '$install->getTableName("some_table")',
                array()
            ),
            array(
                'any',
                '/app/some/path/resource/some-file.php',
                '$install->getTableName(\'some_table\')',
                array(
                    array(
                        'module' => 'SomeModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'some_table'
                    )
                )
            )
        );
    }
}
