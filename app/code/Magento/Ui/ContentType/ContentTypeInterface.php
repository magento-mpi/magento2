<?php
/**
 * {license}
 */

namespace Magento\Ui\ContentType;

use Magento\Ui\UiInterface;

/**
 * Interface ContentTypeInterface
 * @package Magento\Ui\ContentType
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
