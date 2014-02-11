<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Testlink_ConnectorTest extends Unit_PHPUnit_TestCase
{
    /**
     * @var Mage_Testlink_Connector
     */
    protected $_connector;

    protected function setUp()
    {
        Mage_Testlink_Connector::$devKey = "12312341234sdfgsdfgsdf";
        $this->_connector = new Mage_Testlink_Connector;
    }

    protected function tearDown()
    {

    }

    /**
     * @covers Mage_Testlink_Connector::report
     */
    public function testReport()
    {

        $report = $this->_connector->report("71", "72", "f");
        $this->assertNull($report);
    }

    /**
     * @covers       Mage_Testlink_Connector::getProject
     * @dataProvider getProjectDataProvider
     */
    public function testGetProject($project)
    {
        $value = $this->_connector->getProject($project);
        if (is_numeric($project)) {
            $this->assertEquals($value, $project);
        } else {
            $this->assertNull($value);
        }
    }

    public function getProjectDataProvider()
    {
        return array(
            array(null),
            array("null"),
            array(false),
            array(true),
            array("name"),
            array("72"),
            array("72:23423:1234"),
            array("72.1234123.1234"),
        );
    }

    /**
     * @covers       Mage_Testlink_Connector::getTestPlan
     * @dataProvider getTestPlanDataProvider
     */
    public function testGetTestPlan($testplan)
    {
        $tp = $this->_connector->getProject($testplan);
        if (is_numeric($testplan)) {
            $this->assertEquals($tp, $testplan);
        } else {
            $this->assertNull($tp);
        }
    }

    public function getTestPlanDataProvider()
    {
        return array(
            array(null),
            array(null, false),
            array("null"),
            array("null", false),
            array(false),
            array(false, true),
            array(true),
            array(true, false),
            array("name"),
            array("name", '77'),
            array("72"),
            array("72", '77'),
            array("72:23423:1234"),
            array("72:23423:1234", 'name'),
            array("72.1234123.1234"),
            array("72.1234123.1234", 'name'),
        );
    }

    /**
     * @covers       Mage_Testlink_Connector::getBuild
     * @dataProvider getBuildDataProvider
     */
    public function testGetBuild($build)
    {
        $b = $this->_connector->getProject($build);
        if (is_numeric($build)) {
            $this->assertEquals($b, $build);
        } else {
            $this->assertNull($b);
        }
    }

    public function getBuildDataProvider()
    {
        return array(
            array(null),
            array(null, false),
            array("null"),
            array("null", false),
            array(false),
            array(false, true),
            array(true),
            array(true, false),
            array("name"),
            array("name", '77'),
            array("72"),
            array("72", '77'),
            array("72:23423:1234"),
            array("72:23423:1234", 'name'),
            array("72.1234123.1234"),
            array("72.1234123.1234", 'name'),
        );
    }
}