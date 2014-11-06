<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Data\Eav\Option
 * @codeCoverageIgnore
 */
interface AttributeOptionInterface
{
    /**
     * Constants used as data array keys
     */
    const LABEL = 'label';

    const VALUE = 'value';

    const SORT_ORDER = 'sort_order';

    const STORE_LABELS = 'store_labels';

    const IS_DEFAULT = 'is_default';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option value
     *
     * @return string
     */
    public function getValue();

    /**
     * Get option order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * is default
     *
     * @return bool
     */
    public function getIsDefault();

    /**
     * Set option label for store scopes
     *
     * @return \Magento\Eav\Api\Data\AttributeOptionLabelInterface[]
     */
    public function getStoreLabels();
}
