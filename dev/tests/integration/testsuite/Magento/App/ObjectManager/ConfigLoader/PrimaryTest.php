<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\ObjectManager\ConfigLoader;

use Magento\TestFramework\ObjectManager;

class PrimaryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        /** @var \Magento\App\ObjectManager\ConfigLoader\Primary $loader */
        $loader = ObjectManager::getInstance()->get('Magento\App\ObjectManager\ConfigLoader\Primary');
        $result = $loader->load();
        $this->assertNotEmpty($result);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('preferences', $result);
        $this->assertArrayHasKey('Magento\App\State', $result);
    }
} 
