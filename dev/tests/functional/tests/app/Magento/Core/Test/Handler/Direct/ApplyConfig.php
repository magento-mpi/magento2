<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Handler\Direct;

use Mtf\Handler\Direct;
use Mtf\Fixture\FixtureInterface;
use Magento\Framework\App\ObjectManagerFactory;

/**
 * Class ApplyConfig
 * Apply system configuration to application under test
 */
class ApplyConfig extends Direct
{
    /**
     * Create Category
     *
     * @param FixtureInterface $fixture [optional]
     * @return int
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $factory = new ObjectManagerFactory();
        $objectManager = $factory->create(BP, $_SERVER);

        $objectManager->get('Magento\Framework\Config\Scope')->setCurrentScope('adminhtml');

        $objectManager->configure(
            $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader')->load('adminhtml')
        );
        // @codingStandardsIgnoreStart
        $objectManager->configure(
            [
                'preferences' => [
                    'Magento\Framework\Authorization\Policy' => 'Magento\Framework\Authorization\Policy\DefaultPolicy',
                    'Magento\Framework\Authorization\RoleLocator' => 'Magento\Framework\Authorization\RoleLocator\DefaultRoleLocator'
                ]
            ]
        );
        // @codingStandardsIgnoreEnd
        $configFactory = $objectManager->get('Magento\Backend\Model\Config\Factory');

        $sections = $fixture->getData()['sections'];
        foreach ($sections as $section) {
            /** @var \Magento\Backend\Model\Config $configModel */
            $configModel = $configFactory->create(['data' => $section]);
            $configModel->save();
        }
    }
}
