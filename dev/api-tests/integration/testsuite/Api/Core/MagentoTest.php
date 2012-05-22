<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @author      Magento PaaS Team <paas-team@magento.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento info Api tests
 */
class Api_Core_MagentoTest extends Magento_Test_Webservice
{
    /**
     * Test magento magento info retrieving
     */
    public function testInfo()
    {
        $magentoInfo = $this->call('magento.info');
        $this->assertNotEmpty($magentoInfo['magento_version']);
        $this->assertNotEmpty($magentoInfo['magento_edition']);
        $this->assertEquals(Mage::getEdition(), $magentoInfo['magento_edition']);
    }
}
