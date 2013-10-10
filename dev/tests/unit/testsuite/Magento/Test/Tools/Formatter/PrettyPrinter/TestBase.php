<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jrutten
 * Date: 10/7/13
 * Time: 3:28 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Test\Tools\Formatter\PrettyPrinter;

require_once __DIR__ . '/../../../../../../../../tools/PHP-Parser/lib/bootstrap.php';

/**
 * This class is used as a base class for the other tests in this package. Its main job is to include reference to the
 * bootstrap file.
 *
 * Class TestBase
 * @package Magento\Test\Tools\Formatter\PrettyPrinter
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase {
}