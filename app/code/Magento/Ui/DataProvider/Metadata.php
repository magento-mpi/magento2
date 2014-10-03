<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\DataProvider;

use Magento\Ui\DataProvider\Config\Data as Config;
use Magento\Framework\ObjectManager;
/**
 * Class Metadata
 */
class Metadata implements \Iterator, \ArrayAccess
{
    /**
     * @var array
     */
    protected $config;

    protected $metadata = [];


    protected $attributes = [];

    /**
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected $dataSet;

    public function __construct(
        $config,
        ObjectManager $objectManager

    ) {

        $this->config = $config['fields'];
        $this->dataSet = $objectManager->get($config['dataset']);
        $this->initAttributes();

    }

    /**
     * Reset the Collection to the first element
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->config);
    }

    protected function initAttributes()
    {
        if (empty($this->attributes)) {

            foreach ($this->config as $field) {
                if ($field['datatype'] == 'eav') {
                    $attribute = $this->dataSet->getEntity()->getAttribute($field['name']);
                    $this->attributes[$field['name']] = $attribute->getData();

                    $options = [];
                    if ($attribute->usesSource()) {
                        $options = $attribute->getSource()->getAllOptions();
                    }
                    $this->attributes[$field['name']]['options'] = $options;
                    $this->attributes[$field['name']]['validation_rules'] = $attribute->getValidateRules();
                    $this->attributes[$field['name']]['store_label'] = $attribute->getStoreLabel();
                    $this->attributes[$field['name']]['required'] = $attribute->getRequired();
                    $this->attributes[$field['name']]['system'] = $attribute->getSystem();
                    $this->attributes[$field['name']]['user_defined'] = $attribute->getUserDefined();

                }


            }
//            $this->attributeCollection->addFieldToFilter('entity_type_id', $this->dataSet->getEntity()->getTypeId());
//            foreach ($this->attributeCollection as $item) {
//                $this->attributes[$item->getAttributeCode()] = $item->getData();
//            }
        }
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {

        $this->metadata[$this->key()] = [
            'name' => $this->key(),
        ];
        $attributeCodes = [
            'options',
            'validation_rules',
            'attribute_code',
            'frontend_input',
            'input_filter',
            'store_label',
            'visible',
            'required',
            'multiline_count',
            'data_model',
            'frontend_class',
            'frontend_label',
            'note',
            'system',
            'user_defined',
            'backend_type',
            'sort_order'
        ];

        foreach ($attributeCodes as $code) {
            if (isset($this->config[$this->key()][$code])) {
                $this->metadata[$this->key()][$code] = $this->config[$this->key()][$code];
            } else {
                if (isset($this->attributes[$this->key()])) {
                    $this->metadata[$this->key()][$code] = $this->attributes[$this->key()][$code];
                } else {
                    $this->metadata[$this->key()][$code] = null;
                }
            }
        }
        return $this->metadata[$this->key()];
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * Move forward to next element
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->config);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return (bool)$this->key();
    }

    /**
     * Returns price class by code
     *
     * @param string $code
     * @return string
     */
    public function get($code)
    {
        return $this->config[$code];
    }

    /**
     * The value to set.
     *
     * @param string $offset
     * @param string $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /**
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * The offset to unset.
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * The offset to retrieve.
     *
     * @param string $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

} 