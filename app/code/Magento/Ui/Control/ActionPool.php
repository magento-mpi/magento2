<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Control;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class ActionPool
 */
class ActionPool implements ActionPoolInterface
{
    /**
     * Actions toolbar block name
     */
    const ACTIONS_PAGE_TOOLBAR = 'page.actions.toolbar';

    /**
     * Render context
     *
     * @var Context
     */
    protected $context;

    /**
     * Actions pool
     *
     * @var Item[]
     */
    protected $items;

    /**
     * Button factory
     *
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Framework\View\Element\AbstractBlock
     */
    protected $toolbarBlock;

    /**
     * Construct
     *
     * @param Context $context
     * @param ItemFactory $itemFactory
     */
    public function __construct(Context $context, ItemFactory $itemFactory)
    {
        $this->context = $context;
        $this->itemFactory = $itemFactory;
        $this->toolbarBlock = $this->context->getPageLayout()->getBlock(static::ACTIONS_PAGE_TOOLBAR);
    }


    /**
     * Create button container
     *
     * @param string $key
     * @param UiComponentInterface $view
     * @return \Magento\Backend\Block\Widget\Button\Toolbar\Container
     */
    protected function createContainer($key, UiComponentInterface $view)
    {
        $container = $this->context->getPageLayout()->createBlock(
            'Magento\Ui\Control\Container',
            'container-' . $key,
            [
                'data' => [
                    'button_item' => $this->items[$key],
                    'context' => $view
                ]
            ]
        );

        return $container;
    }

    /**
     * Add button
     *
     * @param string $key
     * @param array $data
     * @param UiComponentInterface $view
     * @return void
     */
    public function add($key, array $data, UiComponentInterface $view)
    {
        $data['id'] = isset($data['id']) ? $data['id'] : $key;

        if ($this->toolbarBlock !== false) {
            $this->items[$key] = $this->itemFactory->create();
            $this->items[$key]->setData($data);
            $container = $this->createContainer($key, $view);
            $this->toolbarBlock->setChild($key, $container);
        }
    }

    /**
     * Remove button
     *
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Update button
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function update($key, array $data)
    {
        if (isset($this->items[$key])) {
            $this->items[$key]->setData($data);
        }
    }
}
