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
     * Node name of children data sources
     */
    const CHILD_DATA_SOURCES = 'childDataSources';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected $dataSet;

    /**
     * @var array
     */
    protected $children;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param array $config
     * @param ObjectManager $objectManager
     * @param Manager $manager
     */
    public function __construct(array $config, ObjectManager $objectManager, Manager $manager)
    {
        $this->config = $config['fields'];
        if (isset($config['children'])) {
            $this->config[self::CHILD_DATA_SOURCES] = array_keys($config['children']);
        }
        $this->dataSet = $objectManager->get($config['dataset']);
        $this->manager = $manager;
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
                if (isset($field['source']) && $field['source'] == 'eav') {
                    $attribute = $this->dataSet->getEntity()->getAttribute($field['name']);
                    $this->attributes[$field['name']] = $attribute->getData();

                    $options = [];
                    if ($attribute->usesSource()) {
                        $options = $attribute->getSource()->getAllOptions();
                    }
                    $this->attributes[$field['name']]['options'] = $options;
                    $this->attributes[$field['name']]['required'] = $attribute->getRequired();
                }
            }
        }
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        if ($this->key() == self::CHILD_DATA_SOURCES) {
            foreach ($this->config[$this->key()] as $child) {
                $this->metadata[$this->key()][$child] = $this->manager->getMetadata($child);
            }
            return $this->metadata[$this->key()];
        }
        $this->metadata[$this->key()] = [
            'name' => $this->key(),
        ];
        $options = [];
        if (isset($this->config[$this->key()]['source'])
            && $this->config[$this->key()]['source']== 'option'
        ) {
            $rawOptions = $this->manager->getData(
                $this->config[$this->key()]['reference']['target']
            );
            $options[] = [
                'label' => null,
                'value' => null
            ];
            foreach ($rawOptions as $rawOption) {
                $options[] = [
                    'label' => $rawOption[$this->config[$this->key()]['reference']['neededField']],
                    'value' => $rawOption[$this->config[$this->key()]['reference']['targetField']]

                ];
            }
        }
        $attributeCodes = [
            'options' => ['eav_map' => 'options', 'default' => $options],
            'dataType' => ['eav_map' => 'frontend_input', 'default' => 'text'],
            'filterType' => ['eav_map' => 'input_filter'],
            'formElement' => ['default' => 'input'],
            'visible' => ['eav_map' => 'is_visible', 'default' => true],
            'required' => ['eav_map' => 'required', 'default' => false],
            'label' => ['eav_map' => 'frontend_label'],
            'sortOrder' => ['eav_map' => 'sort_order']
        ];

        foreach ($attributeCodes as  $code => $info) {
            if (isset($this->config[$this->key()][$code])) {
                $this->metadata[$this->key()][$code] = $this->config[$this->key()][$code];
            } else {
                if (isset($this->attributes[$this->key()]) && isset($info['eav_map'])) {
                    $this->metadata[$this->key()][$code] = $this->attributes[$this->key()][$info['eav_map']];
                } else {
                    $this->metadata[$this->key()][$code] = null;
                }
            }
            if (empty($this->metadata[$this->key()][$code]) && isset($info['default'])) {
                $this->metadata[$this->key()][$code] = $info['default'];
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
     * @return string|array
     */
    public function get($code)
    {
        return isset($this->config[$code]) ? $this->config[$code] : false;
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