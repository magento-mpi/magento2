<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento Performance Toolkit
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Class GeneratorTest
 * @package Magento Performance Toolkit
 */
class PerformanceToolkitGeneratorTest extends \Magento\TestFramework\Indexer\TestCase
{
    /**
     * Profile generator working directory
     *
     * @var string
     */
    protected static $_generatorWorkingDir;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public static function setUpBeforeClass()
    {

        self::$_generatorWorkingDir = realpath(__DIR__ . '/../../../../tools/performance_toolkit');
        \Magento\Autoload\IncludePath::addIncludePath(array(self::$_generatorWorkingDir . '/framework'));
        copy(
            self::$_generatorWorkingDir . '/fixtures/tax_rates.csv',
            self::$_generatorWorkingDir . '/fixtures/tax_rates.csv.bak'
        );
        copy(__DIR__ . '/_files/tax_rates.csv', self::$_generatorWorkingDir . '/fixtures/tax_rates.csv');
        parent::setUpBeforeClass();
    }

    /**
     * @magentoAppArea install
     */
    public function testTest()
    {
        $fixturesArray = \Magento\ToolkitFramework\FixtureSet::getInstance()->getFixtures();
        $config = \Magento\ToolkitFramework\Config::getInstance();
        $config->loadConfig(self::$_generatorWorkingDir . '/profiles/small.xml');

        foreach ($fixturesArray as $fixture) {
            $this->applyFixture(self::$_generatorWorkingDir . '/fixtures/' . $fixture);
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink(self::$_generatorWorkingDir . '/fixtures/tax_rates.csv');
        rename(
            self::$_generatorWorkingDir . '/fixtures/tax_rates.csv.bak',
            self::$_generatorWorkingDir . '/fixtures/tax_rates.csv'
        );
    }

    /**
     * Apply fixture file
     *
     * @param string $fixtureFilename
     */
    public function applyFixture($fixtureFilename)
    {
        require $fixtureFilename;
    }

    /**
     * Get object manager
     *
     * @return \Magento\ObjectManager
     */
    public function getObjectManager()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        }
        return $this->_objectManager;
    }

    /**
     * Reset object manager
     *
     * @return \Magento\ObjectManager
     */
    public function resetObjectManager()
    {
        $this->_objectManager = null;
        return $this;
    }
}
