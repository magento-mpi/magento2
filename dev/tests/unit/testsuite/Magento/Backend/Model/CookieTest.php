<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSecure()
    {
        $request = $this->getMock('Magento\App\Request\Http', array('isSecure'), array(), '', false);
        $request->expects($this->once())->method('isSecure')->will($this->returnValue('some value'));

        $response = $this->getMockForAbstractClass('Magento\App\ResponseInterface');
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', null, array(), '', false);
        $storeManager = $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface');

        $model = new Cookie($request, $response, $coreStoreConfig, $storeManager);
        $this->assertEquals('some value', $model->isSecure());
    }
}
