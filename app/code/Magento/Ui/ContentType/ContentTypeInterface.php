<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Ui\ViewInterface;

/**
 * Interface ContentTypeInterface
 */
interface ContentTypeInterface
{
    /**
     * @param ViewInterface $view
     * @param string $template
     * @return mixed
     */
    public function render(ViewInterface $view, $template = '');
}
