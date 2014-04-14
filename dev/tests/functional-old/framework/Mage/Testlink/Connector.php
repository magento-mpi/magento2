<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'class-IXR.php';

class Mage_Testlink_Connector
{

    /**
     * Default server url. Should be overridden in phpunit.xml
     * @var string
     */
    public static $serverURL = "http://localhost//testlink/lib/api/xmlrpc.php";

    /**
     * @var IXR_Client
     */
    private $_client;

    /**
     * Key used to get connection to xml-rpc of testlink (all test cases will be signed from the user whoes id is used)
     *
     * @var string|null
     */
    public static $devKey = null;

    /**
     * Map of status codes for sending to testlink
     *
     * @var array
     */
    public static $tcaseStatusCode = array(
        'passed' => 'p',
        'blocked' => 'b',
        'failed' => 'f',
        'wrong' => 'w',
        'departed' => 'd'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_client = new IXR_Client(Mage_Testlink_Connector::$serverURL);
    }

    /**
     * Generates the report for sending to testlink. Sends it to testlink and returns array with the result
     *
     * @param string $tcaseexternalid
     * @param string $tplanid
     * @param string $status
     * @param string $buildid
     * @param string $notes
     *
     * @return array
     */
    public function report($tcaseexternalid, $tplanid, $status, $buildid=null, $notes=null)
    {
        return $this->reportResult($tcaseexternalid, $tplanid, $buildid, null, $status,
                                   $notes, null, null, null, false, false);
    }

    /**
     * Send the result of test execution of the test case
     *
     * @param string $tCaseExternalId
     * @param string $tPlanId
     * @param string $buildId
     * @param string $buildName
     * @param string $status
     * @param string $notes
     * @param string $bugId
     * @param string $customFields
     * @param string $platformName
     *
     * @return array
     */
    protected function reportResult(
        $tCaseExternalId=null,
        $tPlanId,
        $buildId=null,
        $buildName=null,
        $status,
        $notes=null,
        $bugId=null,
        $customFields=null,
        $platformName=null
    ) {
        $this->_client->debug = false;
        $data = array();
        $data["devKey"] = Mage_Testlink_Connector::$devKey;
        $data["testplanid"] = $tPlanId;

        if (!is_null($bugId)) {
            $data["bugid"] = $bugId;
        }

        if (!is_null($tCaseExternalId)) {
            $data["testcaseexternalid"] = $tCaseExternalId;
        }

        if (!is_null($buildId)) {
            $data["buildid"] = $buildId;
        } elseif (!is_null($buildName)) {
            $data["buildname"] = $buildName;
        }

        if (!is_null($notes)) {
            $data["notes"] = $notes;
        }
        $data["status"] = $status;

        if (!is_null($customFields)) {
            $data["customfields"] = $customFields;
        }

        if (!is_null($platformName)) {
            $data["platformname"] = $platformName;
        }

        $data["overwrite"] = true;

        if ($this->_client->query('tl.reportTCResult', $data)) {
            $response = $this->_client->getResponse();
        } else {
            $response = null;
        }
        return $response;
    }

    /**
     * Performs and action for the executed test case (i.e. sets the status of test case)
     *
     * @param string    $method
     * @param array     $args
     *
     * @return array
     */
    protected function action($method, $args)
    {
        $args["devKey"] = Mage_Testlink_Connector::$devKey;

        if (!$this->_client->query("tl.{$method}", $args)) {
            $response = null;
        } else {
            $response = $this->_client->getResponse();
        }
        return $response;
    }

    /**
     * Gets project's id
     *
     * @param string $name
     *
     * @return string
     */
    public function getProject($name)
    {
        if (!is_numeric($name)) {
            $method = 'getProjects';
            $args = array();
            $projects = $this->action($method, $args);
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    if (isset($project["name"]) && ($project["name"] == $name)) {
                        return isset($project["id"]) ? $project["id"] : null;
                    }
                }
            } else {
                return null;
            }
        } else {
            return $name;
        }
        return null;
    }

    /**
     * Gets array of all tests plans in project
     *
     * @param string    $projectId
     *
     * @return array
     */
    protected function getTestPlans($projectId)
    {
        $plans = array();
        if (is_numeric($projectId)) {
            $method = 'getProjectTestPlans';
            $args = array();
            $args["testprojectid"] = $projectId;
            $plans = $this->action($method, $args);
        }
        return $plans;
    }

    /**
     * Gets the last test plan from project or searches for test plan by name or id
     *
     * @param string        $projectId
     * @param string|null   $testPlan
     *
     * @return array
     */
    public function getTestPlan($projectId, $testPlan=null)
    {
        $plans = $this->getTestPlans($projectId);
        if (isset($testPlan) && !empty($plans)) {
            $plan = $this->_defineTestPlan($testPlan, $plans);
            if (!empty($plan)) {
                return $plan;
            }
        } else {
            return $plan = (!empty($plans)) ? $plans[count($plans) - 1] : array();
        }
        return null;
    }

    /**
     * Defines test plan
     * @param $testPlan
     * @param $plans
     * @return null
     */
    protected function _defineTestPlan($testPlan, $plans)
    {
        if (is_numeric($testPlan)) {
            foreach ($plans as $plan) {
                if (isset($plan['id']) && $plan['id'] == $testPlan) {
                    return $plan;
                }
            }
        } else {
            foreach ($plans as $plan) {
                if (isset($plan['name']) && $plan['name'] == $testPlan) {
                    return $plan;
                }
            }
        }
        return null;
    }

    /**
     * Gets array of all builds from the test plan
     *
     * @param string $testplanId
     *
     * @return array
     */
    protected function getBuilds($testplanId)
    {
        $method = 'getBuildsForTestPlan';
        $args = array();
        $args["testplanid"] = $testplanId;
        return $this->action($method, $args);
    }

    /**
     * Gets current build
     *
     * @param string      $testplanId
     * @param string|null $buildId
     *
     * @return array
     */
    public function getBuild($testplanId, $buildId=null)
    {
        $builds = isset($testplanId) ? $this->getBuilds($testplanId) : array();
        if (!empty($builds)) {
            if (isset($buildId)) {
                $build = $this->_defineBuild($builds, $buildId);
                if (!empty($build)) {
                    return $build;
                }
            } else {
                return $builds[count($builds) -1];
            }
        }
        return array();
    }

    /**
     * Defines build
     * @param $builds
     * @param $buildId
     * @return null
     */
    protected function _defineBuild($builds, $buildId)
    {
        foreach ($builds as $build) {
            if (is_numeric($buildId)) {
                if (isset($build['id']) && $build['id'] == $buildId) {
                    return $build;
                } elseif (isset($build['name']) && $build['name'] == $buildId) {
                    return $build;
                }
            } else {
                if (isset($build['name']) && $build['name'] == $buildId) {
                    return $build;
                }
            }
        }
        return null;
    }

    /**
     * Gets available tests from the test plan in testlink
     *
     * @param string $testplanId
     *
     * @return array
     */
    protected function getTests($testplanId)
    {
        $method = 'getTestCasesForTestPlan';
        $args = array();
        $args["testplanid"] = $testplanId;
        return $this->action($method, $args);
    }
}