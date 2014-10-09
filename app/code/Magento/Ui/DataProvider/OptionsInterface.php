<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider;

/**
 * Class OptionsInterface
 */
interface OptionsInterface
{
    /**
     * Get options
     *
     * @param array $options
     * @return array
     */
    public function getOptions(array $options = []);
}
