<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

/**
 * @method int getViewId()
 * @method string getActionClass()
 * @method array getSubscriptions()
 */
class View extends \Magento\Object
{
    /**
     * @var string
     */
    protected $_idFieldName = 'view_id';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @param ConfigInterface $config
     * @param ActionFactory $actionFactory
     * @param string $viewId
     * @throws \InvalidArgumentException
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        $viewId
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;

        $view = $config->get($viewId);
        if (empty($view) || empty($view['id']) || $view['id'] != $viewId) {
            throw new \InvalidArgumentException('{$viewId} view does not exist.');
        }

        parent::__construct($view);
    }

    public function subscribe()
    {
        foreach ($this->getSubscriptions() as $table) {
        }
    }

    /**
     * Materialize view by IDs in changelog
     */
    public function update()
    {
        $ids = array(); // TODO: Use changelog
        $action = $this->actionFactory->create($this->getActionClass());
        $action->execute($ids);
    }
}