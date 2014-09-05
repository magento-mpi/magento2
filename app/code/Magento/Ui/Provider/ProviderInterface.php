<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Provider;

/**
 * Interface ProviderInterface
 */
interface ProviderInterface
{
    /**
     * @param array $dataRow
     * @return array
     */
    public function provide(array $dataRow);
}