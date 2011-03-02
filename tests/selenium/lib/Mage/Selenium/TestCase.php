<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An extended test case implementation that add usefull helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestCase extends PHPUnit_Extensions_SeleniumTestCase
{

    /**
     * Data helper instance
     *
     * @var Mage_Selenium_DataHelper
     */
    protected $_dataHelper = null;

    /**
     * Data generator helper instance
     *
     * @var Mage_Selenium_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * SUT helper instance
     *
     * @var Mage_Selenium_SutHelper
     */
    protected $_sutHelper = null;

    /**
     * Uid helper instance
     *
     * @var Mage_Selenium_Uid
     */
    protected $_uid = null;

    /**
     * Page helper instance
     *
     * @var Mage_Selenium_PageHelper
     */
    protected $_pageHelper = null;

    /**
     * @TODO
     *
     * @var array
     */
    public $messages = null;

    /**
     * Current application area
     *
     * @var string
     */
    protected $_area = '';

    /**
     * Configuration object instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig = null;

    /**
     * Constructor
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     * @param  array  $browser
     * @throws InvalidArgumentException
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array()) {
        $this->_testConfig = Mage_Selenium_TestConfiguration::initInstance();
        $this->_dataHelper = $this->_testConfig->getDataHelper();
        $this->_dataGenerator = $this->_testConfig->getDataGenerator();
        $this->_sutHelper = $this->_testConfig->getSutHelper();
        $this->_pageHelper = $this->_testConfig->getPageHelper($this, $this->_sutHelper);
        $this->_uid = $this->_testConfig->getUidHelper();
        parent::__construct($name, $data, $dataName, $browser);
        $this->setArea('frontend');
    }

    /**
     * @param  array $browser
     * @return PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected function getDriver(array $browser)
    {
        $driver = $this->_testConfig->driver;
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);
        // @TODO we need separate driver connections if admin url
        // doesn't start with frontend url
        $driver->setBrowserUrl($this->_sutHelper->getBaseUrl());
        $driver->start();
        $this->drivers[] = $driver;
        return $driver;
    }

    /**
     * Data helper methods
     */

    /**
     * Load test data
     *
     * @param string|array $dataSource Data source (e.g. filename in ../data without .yml extension)
     * @param array|null $override Value to override in original data from data source
     * @param string|array|null $randomize Value to randomize
     * @return array
     */
    public function loadData($dataSource, $override=null, $randomize=null)
    {
        $data = $this->_getData($dataSource);

        if (!empty($override) && is_array($override)) {
            foreach ($override as $field => $value) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    /**
     * Generates some random value
     *
     * @param string $type Available types are 'string', 'text', 'email'
     * @param int $length Generated value length
     * @param string|array|null $modifier Value modifier, e.g. PCRE class
     * @param string|null $prefix Prefix to prepend the generated value
     * @return mixed
     */
    public function generate($type='string', $length=100, $modifier=null, $prefix=null)
    {
        $result = $this->_dataGenerator->generate($type, $length, $modifier,$prefix);
        return $result;
    }

    /**
     * Navigation methods
     */

    /**
     * Navigate to a specified frontend page
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function frontend($page='home')
    {
        $this->setArea('frontend');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigate to a specified admin page
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function admin($page='dashboard')
    {
        $this->setArea('admin');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigates to a specified page in the current area
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page)
    {
        $this->open($this->getPageUrl($page));
        $this->_pageHelper->validateCurrentPage();
        return $this;
    }

    /**
     * Navigates to the specified page in the current area
     * and stops current testcase execution if navigation failed
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function navigated($page)
    {
        $this->navigate($page);
        if ($this->_pageHelper->validationFailed()) {
            // @TODO stop further execution of the current test
        }
        return $this;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $page Page identifier
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->_pageHelper->getPageUrl($page);
    }

    /**
     * Set current area
     *
     * @param string $area Area: 'admin' or 'frontend'
     * @return Mage_Selenium_TestCase
     */
    public function setArea($area)
    {
        $this->_area = $area;
        $this->_sutHelper->setArea($area);
        return $this;
    }

    public function clickButton($button)
    {
        // @TODO
        return $this;
    }

    /**
     * Search specified control on the page
     *
     * @param string $controlType
     * @param string $controlName
     * @return mixed
     */

    public function controlIsPresent($controlType, $controlName)
    {
        // @TODO
        return $this;
    }

    public function buttonIsPresent($button)
    {
        // @TODO
        return $this;
    }

    /**
     * Fill form with data
     *
     * @param array $data
     * @return Mage_Selenium_TestCase
     */
    public function fillForm($data)
    {
        // string(62) "http://www.localhost.com/magento-trunk/customer/account/create"
        $url = trim(preg_replace('~' . $this->_sutHelper->getBaseUrl() . '~', '', $this->getLocation(), 1), '/');
        $formData = $this->_getFormData($url);
        if (isset($formData['fields'])) {
            $baseXpath = (isset($formData['xpath'])) ? '//' . $formData['xpath'] : '';
            foreach ($data as $field => $value) {
                if (isset($formData['fields'][$field]))
                $this->type($baseXpath . '//' . $formData['fields'][$field], $value);
            }
        }
        return $this;
    }

    public function searchAndOpen($something)
    {
        // @TODO
        return $this;
    }

    public function errorMessage()
    {
        // @TODO
        return $this;
    }

    public function successMessage()
    {
        // @TODO
        return $this;
    }


    /**
     * Magento helper methods
     */

    public function logoutCustomer()
    {
        // @TODO
        return $this;
    }

    public function loginAdminUser()
    {
        $this->admin('dashboard');
        if ("Dashboard / Magento Admin" !== $this->getTitle()) {
            $this->type('username', $this->_sutHelper->getDefaultAdminUsername());
            $this->type('login', $this->_sutHelper->getDefaultAdminPassword());
            $this->clickAndWait("//input[@value='Login']", 3000);
        }
        return $this;
    }

    /**
     * Selenium driver helper methods
     */

    /**
     * PHPUnit helper methods
     */

    /**
     * Asserts that a condition is true.
     *
     * @param  boolean $condition
     * @param  string  $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        // @TODO
        self::assertThat(true, self::isTrue(), $message);
//        self::assertThat((boolean)$condition, self::isTrue(), $message);
//        self::assertThat($condition, self::isTrue(), $message);
        if (isset($this)) {
            return $this;
        }
    }

    /**
     * Asserts that a condition is false.
     *
     * @param  boolean $condition
     * @param  string  $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        // @TODO
        self::assertThat(false, self::isFalse(), $message);
//        self::assertThat((boolean)$condition, self::isFalse(), $message);
//        self::assertThat($condition, self::isTrue(), $message);
        if (isset($this)) {
            return $this;
        }
    }



    /**
     * Get node from data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    protected function _getData($path='')
    {
        return $this->_testConfig->getDataValue($path);
    }

    /**
     * Get node from uimap data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    protected function _getUimapData($path='')
    {
        return $this->_testConfig->getUimapValue($this->_area, $path);
    }

    /**
     * Get information about form from uimap by mca
     *
     * @param string $mca
     */
    protected function _getFormData($mca)
    {
        $uimap = $this->_getUimapData();
        foreach ($uimap as $key => $page) {
            if (isset($page['mca'])
                    && trim($page['mca'], '/') == $mca
                    && isset($page['uimap'], $page['uimap']['form'])) {
                return $page['uimap']['form'];
            }
        }
        return false;
    }

}
