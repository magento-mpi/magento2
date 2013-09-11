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
class Magento_DesignEditor_Model_Theme_ChangeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for change model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        /** @var $changeModel \Magento\DesignEditor\Model\Theme\Change */
        $changeModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\DesignEditor\Model\Theme\Change');
        $changeModel->setData($this->_getChangeValidData());

        $crud = new Magento_TestFramework_Entity($changeModel, array('change_time' => '2012-06-10 20:00:01'));
        $crud->testCrud();
    }

    /**
     * Get change valid data
     *
     * @return array
     */
    protected function _getChangeValidData()
    {
        /** @var $theme \Magento\Core\Model\Theme */
        /** @var $themeModel \Magento\Core\Model\Theme */
        $theme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento\Core\Model\Theme');
        $themeModel = $theme->getCollection()->getFirstItem();

        return array(
            'theme_id' => $themeModel->getId(),
            'change_time' => '2013-04-10 23:34:19',
        );
    }
}
