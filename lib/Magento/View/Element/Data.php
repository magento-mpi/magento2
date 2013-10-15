<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

use Magento\View\Element;

class Data extends Base implements Element
{
    /**
     * Element type
     */
    const TYPE = 'data';

    /**
     * Wrapped Element Instance
     *
     * @var \Magento\Core\Block\AbstractBlock
     */
    protected $wrappedElement;

    /**
     * @param Element $parent
     * @throws \Exception
     */
    public function register(Element $parent = null)
    {
        if (isset($this->meta['class'])) {
            if (!class_exists($this->meta['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $this->meta['class']));
            }

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

            $this->wrappedElement = $this->objectManager->create($this->meta['class'],
                array(
                    'container' => $this,
                    'data' => $this->arguments,
                )
            );
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
            $this->wrappedElement = $dataGraph->get($this->name);
        }

        $parent->addDataProvider($this->alias, $this->wrappedElement);
    }

    /**
     * Retrieve wrapped element instance
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function getWrappedElement()
    {
        return $this->wrappedElement;
    }

    /**
     * Call method of wrapped element
     *
     * @param string $method
     * @param array $arguments
     */
    public function call($method, array $arguments)
    {
        if ($this->wrappedElement) {
            call_user_func_array(array($this->wrappedElement, $method), $arguments);
        }
    }
}
