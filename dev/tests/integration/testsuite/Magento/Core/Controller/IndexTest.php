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

namespace Magento\Core\Controller;

class IndexTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    public function testNotFoundAction()
    {
        $this->dispatch('core/index/notfound');
        $this->assertEquals('404', $this->getResponse()->getHttpResponseCode());
        $this->assertEquals('Requested resource not found', $this->getResponse()->getBody());
    }
}
