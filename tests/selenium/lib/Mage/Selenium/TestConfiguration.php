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
 * Test run configuration
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestConfiguration
{

    /**
     * Data helper instance
     *
     * @var Mage_Selenium_DataHelper
     */
    public static $dataHelper = null;

    /**
     * Data generator instance
     *
     * @var Mage_Selenium_DataGenerator
     */
    public static $dataGenerator = null;

    /**
     * Initialized browsers connections
     * @var array[int]PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected static $_browsers = array();

    /**
     * Current browser connection
     *
     * @var PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    public static $browser = null;

    /**
     * Initializes test configuration
     */
    public static function init()
    {
        self::$dataHelper = new Mage_Selenium_DataHelper();
        self::$dataGenerator = new Mage_Selenium_DataGenerator();

        // @TODO load from configuration
        $browser = '*chrome';
        $host = '127.0.0.1';
        $port = 5555;

        $connection = new PHPUnit_Extensions_SeleniumTestCase_Driver();
        $connection->setBrowser($browser);
        $connection->setHost($host);
        $connection->setPort($port);
        self::$_browsers[] = $connection;

        // @TODO implement interations outside
        self::$browser = self::$_browsers[0];
    }

}
