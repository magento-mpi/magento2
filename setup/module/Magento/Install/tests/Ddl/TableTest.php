<?php

namespace Magento\Install\Tests\Ddl;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Ddl\Table
     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\Install\Ddl\Table();
    }

    public function testGetSetEngine()
    {
        $this->assertEquals(\Magento\Install\Ddl\Table::ENGINE_INNODB, $this->model->getEngine());//default value
        $engine = \Magento\Install\Ddl\Table::ENGINE_MYISAM;
        $this->model->setEngine($engine);
        $this->assertEquals($engine, $this->model->getEngine());
    }
}
