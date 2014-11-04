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
use Magento\Framework\Validator\UniversalFactory;

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
     * @var UniversalFactory
     */
    protected $universalFactory;

    /**
     * @param ObjectManager $objectManager
     * @param Manager $manager
     * @param array $config
     */
    public function __construct(
        ObjectManager $objectManager,
        Manager $manager,
        UniversalFactory $universalFactory,
        array $config
    ) {
        $this->config = $config;
        if (isset($this->config['children'])) {
            $this->config['fields'][self::CHILD_DATA_SOURCES] = array_keys($config['children']);
        }
        $this->dataSet = $objectManager->get($this->config['dataset']);
        $this->manager = $manager;
        $this->universalFactory = $universalFactory;
        $this->initAttributes();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->config['label'];
    }

    /**
     * Comma separated list of fields which concatenated and form label
     *
     * @return string
     */
    public function getCompositeLabel()
    {
        return isset($this->config['compositeLabel']) ? $this->config['compositeLabel'] : '';
    }

    /**
     * Comma separated list of fields to be displayed in form preview area
     *
     * @return string
     */
    public function getPreviewElements()
    {
        return isset($this->config['previewElements']) ? $this->config['previewElements'] : '';
    }

    /**
     * Reset the Collection to the first element
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->config['fields']);
    }

    protected function initAttributes()
    {
        if (empty($this->attributes)) {
            foreach ($this->config['fields'] as $field) {
                if (isset($field['source']) && $field['source'] == 'eav') {
                    $attribute = $this->dataSet->getEntity()->getAttribute($field['name']);
                    $this->attributes[$field['name']] = $attribute->getData();

                    $options = [];
                    if ($attribute->usesSource()) {
                        $options = $attribute->getSource()->getAllOptions();
                    }
                    $this->attributes[$field['name']]['options'] = $options;
                    $this->attributes[$field['name']]['is_required'] = $attribute->getIsRequired();
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
            foreach ($this->config['fields'][$this->key()] as $child) {
                $this->metadata[$this->key()][$child] = $this->manager->getMetadata($child);
            }
            return $this->metadata[$this->key()];
        }
        $this->metadata[$this->key()] = [
            'name' => $this->key()
        ];
        $options = [];
        if (isset($this->config['fields'][$this->key()]['source'])) {
            if ($this->config['fields'][$this->key()]['source'] == 'option') {
                $rawOptions = $this->manager->getData(
                    $this->config['fields'][$this->key()]['reference']['target']
                );
                $options[] = [
                    'label' => __('Please, select...'),
                    'value' => null
                ];
                foreach ($rawOptions as $rawOption) {
                    $options[] = [
                        'label' => $rawOption[$this->config['fields'][$this->key()]['reference']['neededField']],
                        'value' => $rawOption[$this->config['fields'][$this->key()]['reference']['targetField']]

                    ];
                }
            }
        } else if (isset($this->config['fields'][$this->key()]['optionProvider'])) {
            list($source, $method) = explode('::', $this->config['fields'][$this->key()]['optionProvider']);
            $sourceModel = $this->universalFactory->create($source);
            $options = $sourceModel->$method();
        }

        $attributeCodes = [
            'options' => ['eav_map' => 'options', 'default' => $options],
            'dataType' => ['eav_map' => 'frontend_input', 'default' => 'text'],
            'filterType' => ['default' => 'input_filter'],
            'formElement' => ['default' => 'input'],
            'displayArea' => ['default' => 'body'],
            'visible' => ['eav_map' => 'is_visible', 'default' => true],
            'required' => ['eav_map' => 'is_required', 'default' => false],
            'label' => ['eav_map' => 'frontend_label'],
            'sortOrder' => ['eav_map' => 'sort_order'],
            'notice' => ['eav_map' => 'note'],
            'default' => ['eav_map' => 'default_value'],
            'description' => [],
            'validation' => [],
            'fieldGroup' => []
        ];

        foreach ($attributeCodes as $code => $info) {
            if (isset($this->config['fields'][$this->key()][$code])) {
                $this->metadata[$this->key()][$code] = $this->config['fields'][$this->key()][$code];
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
        return key($this->config['fields']);
    }

    /**
     * Move forward to next element
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->config['fields']);
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
        return isset($this->config['fields'][$code]) ? $this->config['fields'][$code] : false;
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
            $this->config['fields'][] = $value;
        } else {
            $this->config['fields'][$offset] = $value;
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
        return isset($this->config['fields'][$offset]);
    }

    /**
     * The offset to unset.
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->config['fields'][$offset]);
    }

    /**
     * The offset to retrieve.
     *
     * @param string $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return isset($this->config['fields'][$offset]) ? $this->config['fields'][$offset] : null;
    }

} 