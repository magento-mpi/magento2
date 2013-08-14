<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme change test
 */
class Mage_DesignEditor_Model_Theme_ChangeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for change model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        /** @var $changeModel Mage_DesignEditor_Model_Theme_Change */
        $changeModel = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Mage_DesignEditor_Model_Theme_Change');
        $changeModel->setData($this->_getChangeValidData());

        $crud = new Magento_Test_Entity($changeModel, array('change_time' => '2012-06-10 20:00:01'));
        $crud->testCrud();
    }

    /**
     * Get change valid data
     *
     * @return array
     */
    protected function _getChangeValidData()
    {
        /** @var $theme Mage_Core_Model_Theme */
        /** @var $themeModel Mage_Core_Model_Theme */
        $theme = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel = $theme->getCollection()->getFirstItem();

        return array(
            'theme_id' => $themeModel->getId(),
            'change_time' => '2013-04-10 23:34:19',
        );
    }
}
