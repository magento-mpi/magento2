<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Cms;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Cms
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for CMS Page
     *
     * @var Setup\Page
     */
    protected $page;

    /**
     * @param Setup\Page $page
     */
    public function __construct(
        Setup\Page $page
    ) {
        $this->page = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->page->run();
    }
}
