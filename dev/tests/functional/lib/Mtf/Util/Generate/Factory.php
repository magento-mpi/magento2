<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

use Magento\ObjectManager;

/**
 * Class Factory
 * Factory classes generator
 *
 * @deprecated
 * @package Mtf\Util\Generate
 */
class Factory extends AbstractGenerate
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Factory\Block $block
     * @param Factory\Fixture $fixture
     * @param Factory\Handler $handler
     * @param Factory\Page $page
     * @param Factory\Repository $repository
     */
    public function __construct(
        ObjectManager $objectManager,
        Factory\Block $block,
        Factory\Fixture $fixture,
        Factory\Handler $handler,
        Factory\Page $page,
        Factory\Repository $repository
    ) {
        $this->objectManager = $objectManager;
        $this->block = $block;
        $this->fixture = $fixture;
        $this->handler = $handler;
        $this->page = $page;
        $this->repository = $repository;
    }

    /**
     * Generate Handlers
     */
    public function launch()
    {
        $this->block->launch();
        $this->fixture->launch();
        $this->handler->launch();
        $this->page->launch();
        $this->repository->launch();

        return $this->objectManager->get('Magento\App\ResponseInterface');
    }
}
