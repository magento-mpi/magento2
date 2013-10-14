<?php

namespace Magento\View\Element;

use Magento\View\Element;
use Magento\View\Context;

class Data extends Base implements Element
{
    const TYPE = 'data';
    /**
     * Data
     *
     * @var mixed
     */
    protected $data;

    public function register(Element $parent = null)
    {
        if (isset($this->meta['class'])) {
            if (!class_exists($this->meta['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $this->meta['class']));
            }

            if ($this->getChildren()) {
                foreach ($this->getChildren() as $child) {
                    $metaElement = $this->viewFactory->create($child['type'],
                        array(
                            'context' => $this->context,
                            'parent' => $this,
                            'meta' => $child
                        )
                    );
                    $metaElement->register($this);
                }
            }

            $this->data = $this->objectManager->create($this->meta['class'],
                array('container' => $this, 'data' => $this->arguments));
        } elseif (isset($this->meta['service_call'])) {
            $dataGraph = $this->objectManager->get('Magento\Core\Model\DataService\Graph');
            $service = array(
                $this->name => array(
                    'namespaces' => array(
                        $parent->getName() => $this->alias
                    )
                )
            );
            $dataGraph->init($service);

            $this->data = $dataGraph->get($this->name);
        }

        $parent->addDataProvider($this->alias, $this->data);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getWrappedElement()
    {
        return $this->data;
    }

    /**
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
