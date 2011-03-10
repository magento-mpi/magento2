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
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unit test for Data generator helper
 */
class Mage_Selenium_Helper_DataGeneratorTest extends Mage_PHPUnit_TestCase
{
    /**
     * Selenium DataGenerator instance
     *
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    public function  __construct() {
        parent::__construct();
        $this->_dataGenerator = new Mage_Selenium_Helper_DataGenerator($this->_config);
    }

    /**
     * Testing Mage_Selenium_Helper_DataGenerator::generate()
     */
    public function testGenerate()
    {
        // Common executions
        $this->assertEquals(100, strlen($this->_dataGenerator->generate()));

        // string generations
        //$this->assertEquals(100, strlen($this->_dataGenerator->generate('string')));
        //$this->assertEquals(20, strlen($this->_dataGenerator->generate('string', 20)));
        //$this->assertEquals(20, strlen($this->_dataGenerator->generate('string', 20, '')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generate('string', 20, ':alnum:')));

        $this->assertEquals(20, strlen($this->_dataGenerator->generate('string', 20, ':alnum:', '')));
        $this->assertEmpty($this->_dataGenerator->generate('string', 0, ':alnum:', ''));
        $this->assertEmpty($this->_dataGenerator->generate('string', -1, ':alnum:', ''));
        $this->assertEquals(1000000, strlen($this->_dataGenerator->generate('string', 1000000, ':alnum:', '')));

        $this->assertEquals(26, strlen($this->_dataGenerator->generate('string', 20, ':alnum:', 'prefix')));
        $this->assertStringStartsWith('prefix', $this->_dataGenerator->generate('string', 20, '', 'prefix'));

        $this->assertStringMatchesFormat('%s', $this->_dataGenerator->generate('string', 20, ':alnum:'));
        $this->assertStringMatchesFormat('%d', $this->_dataGenerator->generate('string', 20, ':digit:'));

        // text generations
        $this->assertEquals(26, strlen($this->_dataGenerator->generate('text', 20, '', 'prefix')));
        $this->assertStringStartsWith('prefix', $this->_dataGenerator->generate('text', 20, '', 'prefix'));

        $this->assertEquals(100, strlen($this->_dataGenerator->generate('text')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generate('text', 20)));
        $this->assertEmpty($this->_dataGenerator->generate('text', 0));
        $this->assertEmpty($this->_dataGenerator->generate('text', -1));
        $this->assertEquals(1000000, strlen($this->_dataGenerator->generate('text', 1000000)));

        $this->assertEquals(20, strlen($this->_dataGenerator->generate('text', 20, '')));
        $this->assertEquals(26, strlen($this->_dataGenerator->generate('text', 20, '', 'prefix')));
        $this->assertStringStartsWith( 'prefix', $this->_dataGenerator->generate('text', 20, '', 'prefix'));

        $this->assertStringMatchesFormat('%s', $this->_dataGenerator->generate('text', 20, array('class'=>':alnum:')));
        $this->assertRegExp('/[0-9 ]+/', $this->_dataGenerator->generate('text', 20, array('class'=>':digit:')));

        // email generations
        $this->assertEquals(100, strlen($this->_dataGenerator->generate('email')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generate('email', 20, 'valid')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generate('email', 20, 'some_value')));
        $this->assertEmpty($this->_dataGenerator->generate('email', 0));
        $this->assertEmpty($this->_dataGenerator->generate('email', -1));

        $this->assertRegExp("/^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+)*@([a-z0-9-])+(\.([a-z0-9-])+)*\.(([a-z]){2,})$/i",
                $this->_dataGenerator->generate('email', 20, 'valid'));
        $this->_dataGenerator->generate('email');
        $this->assertRegExp('|([a-z0-9_\.\-]+)@([a-z0-9\.\-]+)\.([a-z]{2,4})|is', $this->_dataGenerator->generate('email'));
    }

    /**
     * Testing Mage_Selenium_Helper_DataGenerator::generateEmailAddress()
     */
    public function testGenerateEmailAddress()
    {
        $this->assertNotEmpty($this->_dataGenerator->generateEmailAddress());
        $this->assertEquals(100, strlen($this->_dataGenerator->generateEmailAddress()));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateEmailAddress(20)));
        $this->assertEmpty($this->_dataGenerator->generateEmailAddress(0));
        $this->assertEmpty($this->_dataGenerator->generateEmailAddress(-1));

        $this->assertEquals(20, strlen($this->_dataGenerator->generateEmailAddress(20, 'valid')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateEmailAddress(20, 'invalid')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateEmailAddress(20, 'some_value')));

        $this->assertRegExp("/^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+)*@([a-z0-9-])+(\.([a-z0-9-])+)*\.(([a-z]){2,})$/i",
            $this->_dataGenerator->generateEmailAddress(20, 'valid'));
        $this->assertNotRegExp("/^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+)*@([a-z0-9-])+(\.([a-z0-9-])+)*\.(([a-z]){2,})$/i",
            $this->_dataGenerator->generateEmailAddress(20, 'invalid'));
    }

    /**
     * Testing Mage_Selenium_Helper_DataGenerator::generateRandomString()
     */
    public function testGenerateRandomString()
    {
        $this->assertNotEmpty($this->_dataGenerator->generateRandomString());
        $this->assertEquals(100, strlen($this->_dataGenerator->generateRandomString()));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomString(20)));
        $this->assertEmpty($this->_dataGenerator->generateRandomString(0));
        $this->assertEmpty($this->_dataGenerator->generateRandomString(-1));

        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomString(20, ':alnum:')));

        $this->assertRegExp('|[a-zA-Z0-9]{20}|', $this->_dataGenerator->generateRandomString(20, ':alnum:'));
        $this->assertRegExp('|[a-zA-Z]{20}|', $this->_dataGenerator->generateRandomString(20, ':alpha:'));
        $this->assertRegExp('|[0-9]{20}|', $this->_dataGenerator->generateRandomString(20, ':digit:'));
        $this->assertRegExp('|[a-z]{20}|', $this->_dataGenerator->generateRandomString(20, ':lower:'));
        $this->assertRegExp('|[\.\,\!\#\@\$\%\^\&\*\(\)\-\_\+\=\{\}\"\|\\\'\\\\\/\?\<\>\;\:]{20}|', $this->_dataGenerator->generateRandomString(20, ':punct:'));
        $this->assertRegExp('|[\(\)\[\]\\\\\;\:\,\<\>@]{20}|', $this->_dataGenerator->generateRandomString(20, 'invalid-email'));
    }

    /**
     * Testing Mage_Selenium_Helper_DataGenerator::generateRandomString()
     */
    public function testGenerateRandomText()
    {
        $this->assertNotEmpty($this->_dataGenerator->generateRandomText());
        $this->assertEquals(100, strlen($this->_dataGenerator->generateRandomText()));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomText(20)));
        $this->assertEmpty($this->_dataGenerator->generateRandomText(0));
        $this->assertEmpty($this->_dataGenerator->generateRandomText(-1));

        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomText(20, '')));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomText(20, array('class'=>':alnum:', 'para'=>3))));
        $this->assertEquals(20, strlen($this->_dataGenerator->generateRandomText(20, array('para'=>0))));

        $randomText = $this->_dataGenerator->generateRandomText(50, array('para'=>5));
        $this->assertEquals(5, count(explode("\n", $randomText)));

        $this->assertRegExp('|[a-zA-Z0-9 ]{20}|', $this->_dataGenerator->generateRandomText(20, array('class'=>':alnum:')));
        $this->assertRegExp('|[a-zA-Z ]{20}|', $this->_dataGenerator->generateRandomText(20, array('class'=>':alpha:')));
        $this->assertRegExp('|[0-9 ]{20}|', $this->_dataGenerator->generateRandomText(20, array('class'=>':digit:')));
        $this->assertRegExp('|[a-z ]{20}|', $this->_dataGenerator->generateRandomText(20, array('class'=>':lower:')));
        $this->assertRegExp('|[\.\,\!\#\@\$\%\^\&\*\(\)\-\_\+\=\{\}\"\|\\\'\\\\\/\?\<\>\;\: ]{20}|', $this->_dataGenerator->generateRandomText(20, array('class'=>':punct:')));

    }

    /**
     * Test Mage_Selenium_Helper_DataGenerator::generate() wrong generation type
     *
     * @expectedException Mage_Selenium_Exception
     */
    public function testGenerateException()
    {
        $this->assertNull($this->_dataGenerator->generate('some_string'));
    }

}
