<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture\Urlrewrite;

use Mtf\System\Config,
    Mtf\Factory\Factory,
    Mtf\Fixture\DataFixture,
    Magento\Catalog\Test\Fixture\Category as CategoryFixture;

/**
 * Class Category
 * URL rewrite category fixture
 *
 * @package Magento\Backend\Test\Fixture\Urlrewrite
 */
class Category extends DataFixture
{
    /**
     * Category for which URL rewrite is created
     *
     * @var CategoryFixture
     */
    protected $category;

    /**
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, array $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);
        $this->_placeholders['rewritten_category_request_path'] = array($this, 'getRewrittenRequestPath');
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBackendUrlrewriteCategory($this->_dataConfig, $this->_data);

        $this->category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        $this->category->persist();
    }

    /**
     * Retrieve rewritten request path
     *
     * @return string
     */
    public function getRewrittenRequestPath()
    {
        $categoryPath = str_replace(' ', '-', strtolower($this->category->getCategoryName()));
        return $categoryPath . '-custom-redirect.html';
    }

    /**
     * Retrieve original request path
     *
     * @return string
     */
    public function getOriginalRequestPath()
    {
        $categoryPath = str_replace(' ', '-', strtolower($this->category->getCategoryName()));
        return $categoryPath . '.html';
    }

    /**
     * Retrieve category name to which the product is assigned
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->category->getCategoryName();
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
    }
}
