<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Theme_FilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme_File
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    protected function setUp()
    {
        $this->_model = Mage::getObjectManager()->create('Magento_Core_Model_Theme_File');
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Magento_Core_Model_Theme');
        $this->_theme = $themeModel->getCollection()->getFirstItem();
        $this->_data = array(
            'file_path' => 'main.css',
            'file_type' => 'css',
            'content'   => 'content files',
            'order'     => 0,
            'theme'     => $this->_theme,
            'theme_id'  => $this->_theme->getId(),
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_data = array();
        $this->_theme = null;
    }

    /**
     * Test crud operations for theme files model using valid data
     */
    public function testCrud()
    {
        $this->_model->setData($this->_data);

        $crud = new Magento_Test_Entity($this->_model, array('file_path' => 'rename.css'));
        $crud->testCrud();
    }
}
