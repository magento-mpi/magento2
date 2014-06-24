<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model\Angular;
use Magento\Setup\Model\Navigation;

class StateProvider
{
    /**
     * @var State[]
     */
    protected $states;

    /**
     * @var Navigation
     */
    protected $nav;

    /**
     * @var StateFactory
     */
    protected $stateFactory;

    /**
     * @param Navigation $nav
     * @param StateFactory $stateFactory
     */
    public function __construct(Navigation $nav, StateFactory $stateFactory)
    {
        $this->nav = $nav;
        $this->stateFactory = $stateFactory;
    }

    public function build()
    {
        foreach ($this->nav->getData() as $item) {
            $state = $this->stateFactory->create();
            $state->setName($item['url']);
            $state->setUrl($item['url']);
            $state->setTemplateUrl($item['url']);
            $state->setController($item['controller']);
            $this->states[] = $state;
        }
    }

    public function asJS()
    {
        $result = '$stateProvider';
        foreach ($this->states as $state) {
            $result .= '.state(\'' . $state->getUrl() . '\',' . $state->asJS() . ')';
        }
        return $result . ';';
    }
}
