<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Toolbar_ExitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Exit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_DesignEditor_Block_Toolbar_Exit(array('template' => 'toolbar/exit.phtml'));
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetExitUrl()
    {
        $expected = 'http://localhost/index.php/admin/system_design_editor/exit/';
        $this->assertContains($expected, $this->_block->getExitUrl());
    }
}
