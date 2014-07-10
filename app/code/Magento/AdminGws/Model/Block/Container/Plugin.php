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
    const GROUP_NAME = 'widget_container_buttons_rendering';

    /**
     * @var \Magento\AdminGws\Model\Role
     */
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
     * @var \Magento\AdminGws\Model\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\AdminGws\Model\CallbackList $callbackList
     * @param \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker
     * @param \Magento\AdminGws\Model\ConfigInterface $config
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\AdminGws\Model\CallbackList $callbackList,
        \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker,
        \Magento\AdminGws\Model\ConfigInterface $config
    ) {
        $this->role = $role;
        $this->callbackInvoker = $callbackInvoker;
        $this->callbackList = $callbackList;
        $this->config = $config;
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

        if (!($callback = $this->callbackList->pickCallback(self::GROUP_NAME, $subject))) {
            return $proceed($item);
        }

        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor(self::GROUP_NAME), $subject);

        return $proceed($item);
    }
}
