<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Smart\Suite\Test\Page;

use Mtf\Factory\Factory;
use Mtf\Fixture;
use Mtf\Client\Element\Locator;
use Mtf\Page\Page;
use Magento\Backend\Test\Block\Widget\Form;
use Magento\Backend\Test\Block\Widget\Grid;

class Backend extends Page
{
    const MCA = 'smart/backend';

    /**
     * @param Fixture $fixture
     */
    public function openGridPage(Fixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $url = $_ENV['app_backend_url'] . $dataConfig['url_grid_page'];

        $this->_browser->open($url);
    }

    /**
     * @param Fixture $fixture
     */
    public function openCreatePage(Fixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $url = $_ENV['app_backend_url'] . $dataConfig['url_create_page'];

        $params = isset($dataConfig['create_url_params']) ? $dataConfig['create_url_params'] : array();
        foreach ($params as $paramName => $paramValue) {
            $url .= '/' . $paramName . '/' . $paramValue;
        }

        $this->_browser->open($url);
    }

    /**
     * @param Fixture $fixture
     */
    public function openUpdatePage(Fixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $url = $_ENV['app_backend_url'] . $dataConfig['url_update_page'];

        $params = isset($dataConfig['update_url_params']) ? $dataConfig['update_url_params'] : array();

        foreach ($params as $paramName => $paramValue) {
            $url .= '/' . $paramName . '/' . $paramValue;
        }

        $this->_browser->open($url);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param Fixture $fixture
     * @return Form
     */
    public function getFormBlock(Fixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $rootElement = $this->_browser->find('body', Locator::SELECTOR_CSS);

        $formClass = isset($dataConfig['block_form_class'])
            ? $dataConfig['block_form_class']
            : '\\Magento\\Backend\\Test\\Block\\Widget\\Form';

        /** @var $formBlock Form */
        $formBlock = new $formClass($rootElement, $this->_browser);

        return $formBlock;
    }

    /**
     * @param Fixture $fixture
     * @return Grid
     */
    public function getGridBlock(Fixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $rootElement = $this->_browser->find('body', Locator::SELECTOR_CSS);

        $gridClass = isset($dataConfig['block_grid_class'])
            ? $dataConfig['block_grid_class']
            : '\\Magento\\Backend\\Test\\Block\\Widget\\Grid';

        /** @var $gridBlock Grid */
        $gridBlock = new $gridClass($rootElement);

        return $gridBlock;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param Fixture $fixture
     * @return mixed
     */
    public function assertCreateResult(Fixture $fixture)
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-success"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * @param Fixture $fixture
     * @return mixed
     */
    public function assertUpdateResult(Fixture $fixture)
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-success"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * @param Fixture $fixture
     * @return mixed
     */
    public function assertDeleteResult(Fixture $fixture)
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-success"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }
}
