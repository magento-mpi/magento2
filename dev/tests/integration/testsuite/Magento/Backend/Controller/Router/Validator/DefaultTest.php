<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller\Router\Validator;

/**
 * @magentoAppArea adminhtml
 */
class DefaultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoAppIsolation enabled
     */
    public function testConstructWithNotEmptyAreaFrontName()
    {
        $options = array(
            'areaCode'       => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            'baseController' => 'Magento\Backend\Controller\AbstractAction',
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Controller\Router\DefaultRouter', $options);
    }
}
