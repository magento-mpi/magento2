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
        $objectManager = new \Magento\Core\Model\ObjectManager(new \Magento\Core\Model\Config\Primary(BP, $_SERVER));

        $objectManager->get('Magento\Config\Scope')->setCurrentScope('adminhtml');

        $objectManager->configure(
            $objectManager->get('Magento\Core\Model\ObjectManager\ConfigLoader')->load('adminhtml')
        );

        $objectManager->configure(
            array(
                'preferences' => array(
                    'Magento\Authorization\Policy' => 'Magento\Authorization\Policy\DefaultPolicy',
                    'Magento\Authorization\RoleLocator' => 'Magento\Authorization\RoleLocator\DefaultRoleLocator'
                )));

        $configFactory = $objectManager->get('Magento\Backend\Model\Config\Factory');

        $sections = $fixture->getData()['sections'];
        foreach ($sections as $section) {
            /** @var \Magento\Backend\Model\Config $configModel */
            $configModel = $configFactory->create(array('data' => $section));
            $configModel->save();
        }
    }
}
