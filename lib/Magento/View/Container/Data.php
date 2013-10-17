<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class Data extends Base implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'data';

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param ContainerInterface $parent
     * @throws \Exception
     */
    public function register(ContainerInterface $parent = null)
    {
        if (isset($this->meta['class'])) {
            if (!class_exists($this->meta['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $this->meta['class']));
            }

            foreach ($this->getChildren() as $child) {
                $metaElement = $this->viewFactory->create(
                    $child['type'],
                    array(
                        'context' => $this->context,
                        'parent' => $this,
                        'meta' => $child,
                    )
                );
                $metaElement->register($this);
            }

            $this->data = $this->objectManager->create(
                $this->meta['class'],
                array(
                    'container' => $this,
                    'data' => $this->arguments,
                )
            );
        } elseif (isset($this->meta['service_call'])) {
            $service = array(
                $this->name => array(
                    'namespaces' => array(
                        $parent->getName() => $this->alias,
                    )
                )
            );
            $dataGraph = $this->objectManager->get('Magento\Core\Model\DataService\Graph');
            $dataGraph->init($service);
            $this->data = $dataGraph->get($this->name);
        }

        $parent->addDataProvider($this->alias, $this->data);
    }

    /**
     * Retrieve data provider instance
     *
     * @return mixed
     */
    public function getWrappedElement()
    {
        // TODO: Check if data is object
        return $this->data;
    }

    /**
     * Call to data provider method
     *
     * @param $method
     * @param array $arguments
     */
    public function call($method, array $arguments)
    {
        if ($this->data) {
            call_user_func_array(array($this->data, $method), $arguments);
        }
    }
}
