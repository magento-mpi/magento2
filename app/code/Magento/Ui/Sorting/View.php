<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Sorting;

use Magento\Ui\AbstractView;
use Magento\Ui\Configuration;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Prepare component data
     *
     * @param array $arguments
     * @return $this|void
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

        $config = ['direction' => 'asc'];
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }

        $this->rootComponent = $this->getParentComponent();
        $this->configuration = new Configuration(
            $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
            $this->rootComponent->getName(),
            $config
        );

        $this->renderContext->getStorage()->addComponentsData($this->configuration);

        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $field = $this->configuration->getData('field');
        $direction = $this->configuration->getData('direction');
        if (!empty($field) && !empty($direction)) {
            $this->renderContext->getStorage()->getDataCollection($this->getParentName())->setOrder(
                $this->renderContext->getRequestParam('sort', $field),
                strtoupper($this->renderContext->getRequestParam('dir', $direction))
            );
        }
    }
}
