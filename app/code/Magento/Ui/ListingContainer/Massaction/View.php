<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Massaction;

use Magento\Ui\AbstractView;
use Magento\Ui\Configuration;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * View configuration
     *
     * @var array
     */
    protected $viewConfiguration = [
        'actions' => []
    ];

    /**
     * Prepare component data
     *
     * @param array $arguments
     * @return $this|void
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

        $config = ['actions' => []];
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }
        array_walk_recursive(
            $config,
            function (&$item, $key, $object) {
                if ($key === 'url') {
                    $item = $object->getUrl($item);
                }
            },
            $this
        );

        $this->rootComponent = $this->getParentComponent();
        $this->configuration = new Configuration(
            $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
            $this->rootComponent->getName(),
            $config
        );

        $this->renderContext->getStorage()->addComponentsData($this->configuration);
    }
}
