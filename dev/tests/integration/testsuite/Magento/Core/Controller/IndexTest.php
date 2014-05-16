<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testNotFoundAction()
    {
        $this->dispatch('core/index/notfound');
        $this->assertEquals('404', $this->getResponse()->getHttpResponseCode());
        $this->assertEquals('Requested resource not found', $this->getResponse()->getBody());
    }
}
