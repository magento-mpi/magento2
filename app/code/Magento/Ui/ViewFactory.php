<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\ObjectManager;

/**
 * Class ViewFactory
 */
class ViewFactory
{
    /**
     * Default render
     */
    const DEFAULT_VIEW = 'Magento\Ui\PlaceholderView';

    /**
     * @var ViewInterface[]
     */
    protected $views = [];

    public function __construct(ConfigInterface $config, ObjectManager $objectManager)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $name
     * @return ViewInterface
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!isset($this->views[$name])) {
            $viewConfig = $this->config->getValue('elements/' . $name, []);
            $class = isset($viewConfig['class']) ? $viewConfig['class'] : self::DEFAULT_VIEW . '_' . $name;
            $view = $this->objectManager->create($class, [
                    'data'=> $viewConfig
                ]);
            if (!$view instanceof ViewInterface) {
                throw new \InvalidArgumentException('Invalid view class: ' . $class);
            }
            $this->views[$name] = $view;
        }
        return $this->views[$name];
    }
}
