<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Interface ContentTypeInterface
 */
interface ContentTypeInterface
{
    /**
     * Render data
     *
     * @param UiComponentInterface $view
     * @param string $template
     * @return mixed
     */
    public function render(UiComponentInterface $view, $template = '');
}
