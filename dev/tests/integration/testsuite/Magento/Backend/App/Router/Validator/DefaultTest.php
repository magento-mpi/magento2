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

namespace Magento\Backend\App\Router\Validator;

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
            'areaCode'       => \Magento\Core\Model\App\Area::AREA_ADMINHTML,
            'baseController' => 'Magento\Backend\Controller\AbstractAction',
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\App\Router\DefaultRouter', $options);
    }
}
