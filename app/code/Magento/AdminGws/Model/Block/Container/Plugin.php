<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Block\Container;

class Plugin
{
    protected $role;

    /**
     * @var \Magento\AdminGws\Model\CallbackList
     */
    protected $callbackList;

    /**
     * @var \Magento\AdminGws\Model\CallbackInvoker
     */
    protected $callbackInvoker;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\AdminGws\Model\CallbackList $callbackList
     * @param \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\AdminGws\Model\CallbackList $callbackList,
        \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker
    ) {
        $this->role = $role;
        $this->callbackInvoker = $callbackInvoker;
        $this->callbackList = $callbackList;
    }

    /**
     * Check whether button can be rendered
     *
     * @param \Magento\Backend\Block\Widget\ContainerInterface $subject
     * @param callable $proceed
     * @param \Magento\Backend\Block\Widget\Button\Item $item
     * @return bool
     */
    public function aroundCanRender(
        \Magento\Backend\Block\Widget\ContainerInterface $subject,
        \Closure $proceed,
        \Magento\Backend\Block\Widget\Button\Item $item
    ) {
        if ($this->role->getIsAll()) {
            return $proceed($item);
        }

        if (!($callback = $this->callbackList->pickCallback('block_html_before', $subject))) {
            return $proceed($item);
        }
        /* the $observer is used intentionally */
        $this->callbackInvoker->invoke($callback, 'Magento\AdminGws\Model\Container', $subject);

        return $proceed($item);
    }
}
