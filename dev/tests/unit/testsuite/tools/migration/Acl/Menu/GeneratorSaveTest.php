<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/Tools/Migration/Acl/Menu/Generator.php';

/**
 * Tools_Migration_Acl_Menu_Generator save test case
 */
class Tools_Migration_Acl_Menu_GeneratorSaveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $model Tools_Migration_Acl_Menu_Generator
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_menuFile;

    public function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR . 'save' . DIRECTORY_SEPARATOR;

        $this->_menuFile = $fixturePath . 'menu.xml';
        $this->_model = new Tools_Migration_Acl_Menu_Generator($fixturePath, array(), array(), false);
    }

    public function tearDown()
    {
        unset($this->_model);
        unlink($this->_menuFile);
    }

    public function testSaveMenuFiles()
    {
        $expectedDom = new DOMDocument();
        $expectedDom->load($this->_menuFile. '.dist');

        $actualDom = new DOMDocument();
        $actualDom->load($this->_menuFile. '.dist');

        $menuDomList = array(
            $this->_menuFile => $actualDom
        );
        $this->_model->setMenuDomList($menuDomList);

        $this->_model->saveMenuFiles();
        $this->assertFileExists($this->_menuFile);

        $this->assertEquals($expectedDom->saveXML(), $actualDom->saveXML());
    }
}
