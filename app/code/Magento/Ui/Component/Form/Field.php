<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractView;

/**
 * Class AbstractFormElement
 */
class Field extends AbstractView implements UiComponentInterface
{
    /**
     * @return mixed
     */
    public function renderHeader()
    {
        return $this->getRenderEngine()->render($this, $this->getHeaderTemplate());
    }

    /**
     * Getting template for field header section
     *
     * @return string|false
     */
    public function getHeaderTemplate()
    {
        return isset($this->configuration['header_template']) ? $this->configuration['header_template'] : false;
    }
}
