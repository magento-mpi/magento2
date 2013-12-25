<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Block\Customer\Account;

class LinkTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * Remembered old value of store config
     * @var array
     */
    protected $_rememberedConfig;

    /**
     * @param bool $isEnabled
     * @param bool $isEnabledOnFront
     * @param bool $expected
     * @throws \Exception
     *
     * @dataProvider linkExistsDataProvider
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLinkExists($isEnabled, $isEnabledOnFront, $expected)
    {
        $this->_changeConfig(\Magento\Invitation\Model\Config::XML_PATH_ENABLED, $isEnabled);
        $this->_changeConfig(\Magento\Invitation\Model\Config::XML_PATH_ENABLED_ON_FRONT, $isEnabledOnFront);

        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $session->login('customer@example.com', 'password');

        try {
            $this->dispatch('customer/account/');
            $result = $this->getResponse()->getBody();

            /** @var \Magento\UrlInterface $urlBuilder */
            $urlBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\UrlInterface');
            $url = $urlBuilder->getUrl('magento_invitation');
            $searchString = sprintf('href="%s"', $url);

            if ($expected)
                $this->assertContains($searchString, $result);
            else {
                $this->assertNotContains($searchString, $result);
            }
        } catch (\Exception $e) {
            $this->_restoreConfig();
            throw $e;
        }
        $this->_restoreConfig();
    }

    /**
     * @return array
     */
    public function linkExistsDataProvider()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false),
        );
    }

    /**
     * Sets new value to store config path, remembers old value
     *
     * @param  $path
     * @param  $value
     * @return \Magento\Invitation\Block\FormTest
     */
    protected function _changeConfig($path, $value)
    {
        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $oldValue = $store->getConfig($path);
        $store->setConfig($path, $value);

        if (!$this->_rememberedConfig) {
            $this->_rememberedConfig = array(
                'path' => $path,
                'old_value' => $oldValue
            );
        }
        return $this;
    }

    /**
     * Restores previously remembered store config value
     *
     * @return \Magento\Invitation\Block\FormTest
     */
    protected function _restoreConfig()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->setConfig($this->_rememberedConfig['path'], $this->_rememberedConfig['old_value']);
        $this->_rememberedConfig = null;
        return $this;
    }
}
