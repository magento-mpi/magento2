<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ToolkitFramework\ImportExport\Fixture\Complex;

/**
 * Class ComplexGeneratorTest
 *
 */
class ComplexGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Pattern instance
     *
     * @var \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern
     */
    protected $_pattern;

    /**
     * Get pattern instance
     *
     * @return \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern
     */
    protected function getPattern()
    {
        if (!$this->_pattern instanceof \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern) {
            $patternData = array(
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
            );
            $this->_pattern = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern();
            $this->_pattern->setHeaders(array_keys($patternData[0]));
            $this->_pattern->setRowsSet($patternData);
        }
        return $this->_pattern;
    }

    /**
     * Test complex generator iterator interface
     *
     * @return void
     */
    public function testIteratorInterface()
    {
        $model = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Generator($this->getPattern(), 2);
        $rows = array();
        foreach ($model as $row) {
            $rows[] = $row;
        }
        $this->assertEquals(
            array(
                array('id' => '1', 'name' => 'Static', 'calculated' => 10),
                array('id' => '',  'name' => 'xxx 1',  'calculated' => ''),
                array('id' => '',  'name' => 'yyy 1',  'calculated' => ''),
                array('id' => '2', 'name' => 'Static', 'calculated' => 20),
                array('id' => '',  'name' => 'xxx 2',  'calculated' => ''),
                array('id' => '',  'name' => 'yyy 2',  'calculated' => ''),
            ),
            $rows
        );
    }

    /**
     * Test generator getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        $model = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Generator($this->getPattern(), 4);
        for ($i = 0; $i < 32; $i++) {
            $this->assertEquals($model->getIndex($i), floor($i / $this->getPattern()->getRowsCount()) + 1);
        }
    }
}
