<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Control;

use Magento\Ui\Context;
use Magento\Ui\ViewInterface;
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
     * Buttons pool
     *
     * @var Button[]
     */
    protected $buttons;

    /**
     * Button factory
     *
     * @var ItemFactory
     */
    protected $buttonFactory;

    /**
     * Construct
     *
     * @param Context $context
     */
    public function __construct(Context $context, ItemFactory $buttonFactory)
    {
        $this->context = $context;
        $this->buttonFactory = $buttonFactory;
    }


    /**
     * Create button container
     *
     * @param string $key
     * @param ViewInterface $view
     * @return \Magento\Backend\Block\Widget\Button\Toolbar\Container
     */
    protected function createContainer($key, ViewInterface $view)
    {
        $container = $this->context->getPageLayout()->createBlock(
            'Magento\Ui\Control\Container',
            'container-' . $key,
            [
                'data' => [
                    'button_item' => $this->buttons[$key],
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
     * @param ViewInterface $view
     * @return void
     */
    public function addButton($key, array $data, ViewInterface $view)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $toolbarBlock */
        $data['id'] = isset($data['id']) ? $data['id'] : $key;
        $toolbarBlock = $this->context->getPageLayout()->getBlock(static::ACTIONS_PAGE_TOOLBAR);

        if ($toolbarBlock !== false) {
            $this->buttons[$key] = $this->buttonFactory->create();
            $this->buttons[$key]->setData($data);
            $container = $this->createContainer($key, $view);
            $toolbarBlock->setChild($key, $container);
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
        unset($this->buttons[$key]);
    }

    /**
     * Update button
     *
     * @param $key
     * @param array $data
     * @return void
     */
    public function update($key, array $data)
    {
        if (isset($this->buttons[$key])) {
            $this->buttons[$key]->setData($data);
        }
    }
}
