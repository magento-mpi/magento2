<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_Import_Source_CsvTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException LogicException
     */
    public function testConstructException()
    {
        new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/invalid_file');
    }

    public function testConstructStream()
    {
        $stream = 'data://text/plain;base64,' . base64_encode("column1,column2\nvalue1,value2\n");
        $model = new \Magento\ImportExport\Model\Import\Source\Csv($stream);
        foreach ($model as $value) {
            $this->assertSame(array('column1' => 'value1', 'column2' => 'value2'), $value);
        }
    }

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param array $expectedColumns
     * @dataProvider optionalArgsDataProvider
     */
    public function testOptionalArgs($delimiter, $enclosure, $expectedColumns)
    {
        $model = new \Magento\ImportExport\Model\Import\Source\Csv(
            __DIR__ . '/_files/test.csv', $delimiter, $enclosure);
        $this->assertSame($expectedColumns, $model->getColNames());
    }

    /**
     * @return array
     */
    public function optionalArgsDataProvider()
    {
        return array(
            array(',', '"', array('column1', 'column2')),
            array(',', "'", array('column1', '"column2"')),
            array('.', '"', array('column1,"column2"')),
        );
    }

    public function testRewind()
    {
        $model = new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/_files/test.csv');
        $this->assertSame(-1, $model->key());
        $model->next();
        $this->assertSame(0, $model->key());
        $model->next();
        $this->assertSame(1, $model->key());
        $model->rewind();
        $this->assertSame(0, $model->key());
        $model->next();
        $model->next();
        $this->assertSame(2, $model->key());
        $this->assertSame(array('column1' => '5','column2' => ''), $model->current());
    }
}
