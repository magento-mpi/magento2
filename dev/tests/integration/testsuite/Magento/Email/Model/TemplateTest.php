<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Zend_Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mail;

    protected function setUp()
    {
        $this->_mail = $this->getMock(
            'Zend_Mail',
            array('send', 'addTo', 'addBcc', 'setReturnPath', 'setReplyTo'),
            array('utf-8')
        );
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->getMockBuilder(
            'Magento\Email\Model\Template'
        )->setMethods(
            array('_getMail')
        )->setConstructorArgs(
            array(
                $objectManager->get('Magento\Framework\Model\Context'),
                $objectManager->get('Magento\Framework\View\DesignInterface'),
                $objectManager->get('Magento\Framework\Registry'),
                $objectManager->get('Magento\Core\Model\App\Emulation'),
                $objectManager->get('Magento\Store\Model\StoreManager'),
                $objectManager->create('Magento\Framework\App\Filesystem'),
                $objectManager->create('Magento\Framework\View\Url'),
                $objectManager->create('Magento\Framework\View\FileSystem'),
                $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface'),
                $objectManager->get('Magento\Email\Model\Template\FilterFactory'),
                $objectManager->get('Magento\Email\Model\Template\Config')
            )
        )->getMock();
        $objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
        $this->_model->expects($this->any())->method('_getMail')->will($this->returnCallback(array($this, 'getMail')));
        $this->_model->setSenderName('sender')->setSenderEmail('sender@example.com')->setTemplateSubject('Subject');
    }

    /**
     * Return a disposable \Zend_Mail instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend_Mail
     */
    public function getMail()
    {
        return clone $this->_mail;
    }

    public function testSetGetTemplateFilter()
    {
        $filter = $this->_model->getTemplateFilter();
        $this->assertSame($filter, $this->_model->getTemplateFilter());
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getStore()->getId(),
            $filter->getStoreId()
        );

        $filter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Email\Model\Template\Filter'
        );
        $this->_model->setTemplateFilter($filter);
        $this->assertSame($filter, $this->_model->getTemplateFilter());
    }

    public function testLoadDefault()
    {
        $this->_model->loadDefault('customer_create_account_email_template');
        $this->assertNotEmpty($this->_model->getTemplateText());
        $this->assertNotEmpty($this->_model->getTemplateSubject());
        $this->assertNotEmpty($this->_model->getOrigTemplateVariables());
        $this->assertInternalType('array', \Zend_Json::decode($this->_model->getOrigTemplateVariables()));
        $this->assertNotEmpty($this->_model->getTemplateStyles());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetProcessedTemplate()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\AreaList'
        )->getArea(
            \Magento\Framework\App\Area::AREA_FRONTEND
        )->load();
        $this->_setNotDefaultThemeForFixtureStore();
        $expectedViewUrl = 'static/frontend/Magento/plushe/en_US/Magento_Theme/favicon.ico';
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplate());
        $this->_model->setDesignConfig(
            array(
                'area' => 'frontend',
                'store' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Store\Model\StoreManagerInterface'
                )->getStore(
                    'fixturestore'
                )->getId()
            )
        );
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplate());
    }

    /**
     * Set 'Magento/plushe' for the 'fixturestore' store.
     * Application isolation is required, if a test uses this method.
     */
    protected function _setNotDefaultThemeForFixtureStore()
    {
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Design\ThemeInterface'
        );
        $theme->load('Magento/plushe', 'theme_path');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Config\MutableScopeConfigInterface'
        )->setValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            $theme->getId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            'fixturestore'
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/design_change.php
     */
    public function testGetProcessedTemplateDesignChange()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\AreaList'
        )->getArea(
            \Magento\Framework\App\Area::AREA_FRONTEND
        )->load();
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsWith(
            'static/frontend/Magento/plushe/en_US/Magento_Theme/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetProcessedTemplateSubject()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\AreaList'
        )->getArea(
            \Magento\Framework\App\Area::AREA_FRONTEND
        )->load();
        $this->_setNotDefaultThemeForFixtureStore();
        $expectedViewUrl = 'static/frontend/Magento/plushe/en_US/Magento_Theme/favicon.ico';
        $this->_model->setTemplateSubject('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
        $this->_model->setDesignConfig(
            array(
                'area' => 'frontend',
                'store' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Store\Model\StoreManagerInterface'
                )->getStore(
                    'fixturestore'
                )->getId()
            )
        );
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDefaultEmailLogo()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\AreaList'
        )->getArea(
            \Magento\Framework\App\Area::AREA_FRONTEND
        )->load();
        $this->assertStringEndsWith(
            'static/frontend/Magento/blank/en_US/Magento_Email/logo_email.gif',
            $this->_model->getDefaultEmailLogo()
        );
    }
}
