<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner configuration/source model
 */
namespace Magento\Banner\Model;

class Config implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_bannerTypes = [];

    /**
     * @param array $bannerTypes
     */
    public function __construct(array $bannerTypes = [])
    {
        $this->_bannerTypes = $bannerTypes;
    }

    /**
     * Banner types getter
     * Invokes translations to labels.
     *
     * @param bool $sorted
     * @param bool $withEmpty
     * @return array
     */
    public function getTypes($sorted = true, $withEmpty = false)
    {
        $result = [];
        foreach ($this->_bannerTypes as $type => $label) {
            $result[$type] = __($label);
        }
        if ($sorted) {
            asort($result);
        }
        if ($withEmpty) {
            return array_merge(['' => __('-- None --')], $result);
        }
        return $result;
    }

    /**
     * Get types as a source model result
     *
     * @param bool $simplified
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($simplified = false, $withEmpty = true)
    {
        $types = $this->getTypes(true, $withEmpty);
        if ($simplified) {
            return $types;
        }
        $result = [];
        foreach ($types as $key => $label) {
            $result[] = ['value' => $key, 'label' => $label];
        }
        return $result;
    }

    /**
     * Check provided types string as comma-separated against available types
     *
     * @param string|array $types
     * @return array
     */
    public function explodeTypes($types)
    {
        $availableTypes = $this->getTypes(false);
        $result = [];
        if ($types) {
            if (is_string($types)) {
                $types = explode(',', $types);
            }
            foreach ($types as $type) {
                if (isset($availableTypes[$type])) {
                    $result[] = $type;
                }
            }
        }
        return $result;
    }
}
