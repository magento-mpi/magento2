<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Ui\UiInterface;

/**
 * Interface ContentTypeInterface
 */
interface ContentTypeInterface
{
    /**
     * @param UiInterface $ui
     * @param array $data
     * @param array $configuration
     * @return mixed
     */
    public function render(UiInterface $ui, array $data, array $configuration);
}
