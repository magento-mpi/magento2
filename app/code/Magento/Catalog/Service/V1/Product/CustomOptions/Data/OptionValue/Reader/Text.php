<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

class Text extends DefaultReader
{
    /**
     * {@inheritdoc}
     */
    protected function getCustomAttributes(\Magento\Catalog\Model\Product\Option $option)
    {
        return [OptionValue::MAX_CHARACTERS => $option->getMaxCharacters()];
    }
}
