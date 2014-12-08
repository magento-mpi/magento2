<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture;

use Magento\Cms\Test\Repository\Page as Repository;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Page
 * CMS page
 *
 */
class Page extends DataFixture
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->getData('fields/title/value');
    }

    /**
     * Get page identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getData('fields/identifier/value');
    }

    /**
     * Get page content
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->getData('fields/content/value/content');
    }

    /**
     * Initialize fixture data
     *
     * @return void
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()->getMagentoCmsPage($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData(Repository::PAGE);
    }
}
