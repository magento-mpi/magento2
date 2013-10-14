<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Handler\Direct;

use Mtf\Fixture;
use Mtf\Handler\Direct;
use Mtf\Factory\Factory;

/**
 * Class ApplyConfig
 * Apply system configuration to application under test
 *
 * @package Magento\Catalog\Test\Handler\Direct
 */
class ApplyConfig extends Direct
{
    /**
     * Create Category
     *
     * @param Fixture $fixture [optional]
     * @return int
     */
    public function execute(Fixture $fixture = null)
    {
        $objectManager = \Mage::getObjectManager();
        if ($objectManager == null) {
            $objectManager = new \Mage_Core_Model_ObjectManager(new \Mage_Core_Model_Config_Primary(BP, $_SERVER));
        }

        $objectManager->configure($objectManager->get('Mage_Core_Model_ObjectManager_ConfigLoader')->load('adminhtml'));

        $objectManager->configure(
            array(
                'preferences' => array(
                    'Magento_Authorization_Policy' => 'Magento_Authorization_Policy_Default',
                    'Magento_Authorization_RoleLocator' => 'Magento_Authorization_RoleLocator_Default'
                )));

        $configFactory = $objectManager->get('Mage_Backend_Model_Config_Factory');

        $sections = $fixture->getData()['sections'];
        foreach ($sections as $section) {
            /** @var \Mage_Backend_Model_Config $configModel */
            $configModel = $configFactory->create(array('data' => $section));
            $configModel->save();
        }
    }
}
