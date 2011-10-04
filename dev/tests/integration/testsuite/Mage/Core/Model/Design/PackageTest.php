<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Design_PackageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        $fixtureDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files';
        Mage::app()->getConfig()->getOptions()->setDesignDir($fixtureDir . DIRECTORY_SEPARATOR . 'design');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setArea('frontend')
            ->setPackageName('test')
            ->setTheme('default');
    }

    /**
     * @dataProvider getTemplateFilenameDataProvider
     */
    public function testGetTemplateFilename($file, $params)
    {
        $this->assertFileExists($this->_model->getTemplateFilename($file, $params));
    }

    public function getTemplateFilenameDataProvider()
    {
        return array(
            array('template.phtml', array('_module' => 'Mage_Core')),
            array('Mage_Core::template.phtml', array()),
            array('Mage_Core::template.phtml', array('_module' => 'Overriden_Module')),
            array('template.phtml'),
        );
    }
}
