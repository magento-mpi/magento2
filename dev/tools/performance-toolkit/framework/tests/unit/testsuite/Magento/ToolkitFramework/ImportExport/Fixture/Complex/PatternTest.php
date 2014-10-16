<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ToolkitFramework\ImportExport\Fixture\Complex;

/**
 * Class PatternTest
 *
 */
class PatternTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get pattern object
     *
     * @param array $patternData
     *
     * @return \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern
     */
    protected function getPattern($patternData)
    {
        $pattern = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern();
        $pattern->setHeaders(array_keys($patternData[0]));
        $pattern->setRowsSet($patternData);
        return $pattern;
    }

    /**
     * Data source for pattern
     *
     * @return array
     */
    public function patternDataPrivider()
    {
        $result = array(
            0 => array(
                array(
                    array(
                        'id' => '%s',
                        'name' => 'Static',
                        'calculated' => function ($index) {
                            return $index * 10;
                        },
                    ),
                    array(
                        'name' => 'xxx %s'
                    ),
                    array(
                        'name' => 'yyy %s'
                    ),
                ),
                'ecpectedCount'      => 3,
                'expectedRowsResult' => array(
                    array('id' => '1', 'name' => 'Static', 'calculated' => 10),
                    array('id' => '',  'name' => 'xxx 1',  'calculated' => ''),
                    array('id' => '',  'name' => 'yyy 1',  'calculated' => ''),
                )
            ),
            1 => array(
                array(
                    array(
                        'id' => '%s',
                        'name' => 'Dynamic %s',
                        'calculated' => 'calc %s',
                    )
                ),
                'ecpectedCount' => 1,
                'expectedRowsResult' => array(
                    array('id' => '1', 'name' => 'Dynamic 1', 'calculated' => 'calc 1'),
                )
            )
        );
        return $result;
    }

    /**
     * Test pattern object
     *
     * @param array $patternData
     * @param int $expectedRowsCount
     * @param array $expectedRowsResult
     *
     * @dataProvider patternDataPrivider
     * @test
     *
     * @return void
     */
    public function testPattern($patternData, $expectedRowsCount, $expectedRowsResult)
    {
        $pattern = $this->getPattern($patternData);
        $this->assertEquals($pattern->getRowsCount(), $expectedRowsCount);
        foreach ($expectedRowsResult as $key => $expectedRow) {
            $this->assertEquals($expectedRow, $pattern->getRow(floor($key / $pattern->getRowsCount()) + 1, $key));
        }
    }
}
