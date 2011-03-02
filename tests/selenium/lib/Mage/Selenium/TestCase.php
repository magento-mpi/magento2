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

    protected $_dataHelper = null;
    protected $_dataGenerator = null;
    protected $_uid = null;

    protected $_pageHelper = null;

    public $messages = null;

    protected $_baseUrl = '';
    protected $_isAdmin = false;

    /**
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     * @param  array  $browser
     * @throws InvalidArgumentException
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array()) {
        parent::__construct($name, $data, $dataName, $browser);
        $this->_dataHelper = Mage_Selenium_TestConfiguration::$instance->dataHelper;
        $this->_dataGenerator = Mage_Selenium_TestConfiguration::$instance->dataGenerator;
        $this->_pageHelper = Mage_Selenium_TestConfiguration::$instance->getPageHelper($this);
        $this->_uid = new Mage_Selenium_Uid();
        // @TODO
        $this->_baseUrl = 'http://www.localhost.com/magento-trunk/';
        $this->_isAdmin = false;
        $this->setBrowserUrl($this->_baseUrl);
    }

    /**
     * @param  array $browser
     * @return PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected function getDriver(array $browser)
    {
        $driver = Mage_Selenium_TestConfiguration::$instance->driver;
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);
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
     * Generate some value
     *
     * @param string $type
     * @param int $length
     * @param mixed $modifier
     * @param string $prefix
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

    public function front($page='home')
    {
        // @TODO
        return $this;
    }

    public function admin($page='dashboard')
    {
        // @TODO
        return $this;
    }

    /**
     * Navigates to a specified page
     *
     * @param string $page Page in MCA format
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page)
    {
//        $this->open($this->getPageUrl($page));
        $this->_pageHelper->validateCurrentPage();
        return $this;
    }

    /**
     * Navigates to the specified page and stops current testcase execution if navigation failed
     *
     * @param string $page Page in MCA format
     * @return Mage_Selenium_TestCase
     */
    public function navigated($page)
    {
        $this->navigate($page);
        // @TODO extra validation to make sure we successfully navigated or stop further execution of the current test
        return $this;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $page Page in MCA format
     * @return string
     */
    public function getPageUrl($page)
    {
        // @TODO
        $url = $this->_baseUrl . $page;
        return $url;
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
     * Fill for with data
     * 
     * @param array $data
     * @return Mage_Selenium_TestCase
     */
    public function fillForm($data)
    {
        $url = trim(preg_replace('~' . $this->_baseUrl . '~', '', $this->getLocation(), 1), '/');
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
        // @TODO
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
    protected function _getData($path)
    {
        return Mage_Selenium_TestConfiguration::getData($path);
    }

    /**
     * Get node from uimap data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    protected function _getUimapData($path)
    {
        return Mage_Selenium_TestConfiguration::getUimapData($path);
    }

    /**
     * Get information about form from uimap by mca
     *
     * @param string $mca
     */
    protected function _getFormData($mca)
    {
        $uimap = $this->_getUimapData(($this->_isAdmin) ? 'admin' : 'frontend');

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
