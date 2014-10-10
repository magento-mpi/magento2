<?php
/**
 * Product attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Product\Attribute;

/**
 * Class Option
 *
 * Should be implemented by new model \Magento\Eav\Model\Attribute\Option
 * implied partial refactoring of \Magento\Eav\Model\Resource\Entity\Attribute
 *
 * @codeCoverageIgnore
 */
/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Data\Eav\Option
 * @todo remove DUPLICATE
 * @see \Magento\Catalog\Api\Data\Eav\AttributeOptionInterface
 */
interface OptionInterface
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';

    const VALUE = 'value';

    const ORDER = 'order';

    const STORE_LABELS = 'store_labels';

    const IS_DEFAULT = 'default';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option value
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Get option order
     *
     * @return int|null
     */
    public function getOrder();

    /**
     * is default
     *
     * @return bool|null
     */
    public function isDefault();

    /**
     * Get storeId => label pairs for option
     *
     * @return \Magento\Service\Framework\KeyValueInterface[]|null
     */
    public function getStoreLabels();
}
