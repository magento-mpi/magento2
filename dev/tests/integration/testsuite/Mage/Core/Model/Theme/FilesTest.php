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

class Mage_Core_Model_Theme_FilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for theme files model using valid data
     */
    public function testCrud()
    {
        /** @var $themeModel Mage_Core_Model_Theme_Files */
        $filesModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Files');
        $filesData = $this->_getThemeFilesValidData();

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollection()->getFirstItem();

        $filesData['theme_id'] = $theme->getId();
        $filesModel->setData($filesData);

        $crud = new Magento_Test_Entity($filesModel, array('file_name' => 'rename.css'));
        $crud->testCrud();
    }

    /**
     * Get theme files valid data
     *
     * @return array
     */
    protected function _getThemeFilesValidData()
    {
        return array(
            'file_name' => 'main.css',
            'file_type' => 'css',
            'content'   => 'content files',
            'order'     => 0,
        );
    }

}
