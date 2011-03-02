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
class Mage_Selenium_TestCase extends PHPUnit_Framework_TestCase
{

    protected $_dataHelper = null;
    protected $_dataGenerator = null;
    protected $uid = null;

    protected $browser = null;
    protected $_pageObject = null;

    public $messages = null;

    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->_dataHelper = Mage_Selenium_TestConfiguration::$dataHelper;
        $this->_dataGenerator = Mage_Selenium_TestConfiguration::$dataGenerator;
        $this->browser = Mage_Selenium_TestConfiguration::$browser;
        $this->uid = new Mage_Selenium_Uid();
    }

    /**
     * Data helper methods
     */

    /**
     *
     * @param string|array $dataSource
     * @param array|null $override
     * @param string|array|null $randomize
     * @return array
     */
    public function data($dataSource, $override=null, $randomize=null)
    {
        $data = null;
        // @TODO
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
     * Page helper methods
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

    public function navigate($page)
    {
        // @TODO
        return $this;
    }

    public function clickButton($button)
    {
        // @TODO
        return $this;
    }

    public function navigated($page)
    {
        // @TODO
        return $this;
    }

    public function linkIsPresent($link)
    {
        // @TODO
        return $this;
    }

    public function fillForm($data)
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

    public static function assertTrue($condition, $message='')
    {
        // @TODO
    }

    public static function assertFalse($condition, $message='')
    {
        // @TODO
    }

}
