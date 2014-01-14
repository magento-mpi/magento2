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

namespace Magento\Cms\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Magento\Cms\Test\Repository\Page as Repository;

/**
 * Class Page
 * CMS page
 *
 * @package Magento\Cms\Test\Fixture
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
        return $this->getData('fields/page_title/value');
    }

    /**
     * Get page identifier
     *
     * @return string
     */
    public function getPageIdentifier()
    {
        return $this->getData('fields/page_identifier/value');
    }

    /**
     * Get page content
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->getData('fields/page_content/value');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()->getMagentoCmsPage($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData(Repository::PAGE);
    }
}
