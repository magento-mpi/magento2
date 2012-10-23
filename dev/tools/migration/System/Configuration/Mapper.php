<?php

class Tools_Migration_System_Configuration_Mapper
{
    /**
     * Attribute maps
     * oldName => newName
     * @var array
     */
    protected $_attributeMaps;

    /**
     * @var array
     */
    protected $_allowedFieldNames;

    public function __construct()
    {
        $this->_attributeMaps = array(
            'sort_order' => 'sortOrder',
            'show_in_default' => 'showInDefault',
            'show_in_store' => 'showInStore',
            'show_in_website' => 'showInWebsite',
            'frontend_type' => 'type',
        );

        $this->_allowedFieldNames = array(
            'label',
            'comment',
            'tooltip',
            'frontend_class',
            'validate',
            'can_be_empty',
            'if_module_enabled',
            'frontend_model',
            'backend_model',
            'source_model',
            'config_path',
            'base_url',
            'upload_dir',
            'button_url',
            'button_label',
            'depends',
            'more_url',
            'demo_url',
        );
    }

    public function transform(array $config)
    {
        $output = array();
        $output['comment'] = $config['comment'];
        $nodes = array();
        $config = $config['config'];

        $tabs = isset($config['tabs']) ? $config['tabs'] : array();
        $sections = isset($config['sections']) ? $config['sections'] : array();

        foreach ($tabs as $tabName => $tabConfig) {
            $nodes[] = $this->_transformElement($tabName, $tabConfig, 'tab');
        }

        foreach ($sections as $sectionName => $sectionConfig) {
            $groupsConfig = isset($sectionConfig['groups']) ? $sectionConfig['groups'] : array();
            if (false === empty($groupsConfig)) {
                unset($sectionConfig['groups']);
            }
            $section = $this->_transformElement($sectionName, $sectionConfig, 'section');

            $groups = array();
            foreach ($groupsConfig as $groupName => $groupConfig) {
                $fieldsConfig = isset($groupConfig['fields']) ? $groupConfig['fields'] : array();
                if (false === empty($fieldsConfig)) {
                    unset($groupConfig['fields']);
                }

                $group = $this->_transformElement($groupName, $groupConfig, 'group');

                $fields = array();
                foreach ($fieldsConfig as $fieldName => $fieldConfig) {
                    $fields[] = $this->_transformElement($fieldName, $fieldConfig, 'field', $this->_allowedFieldNames);
                }

                if (false === empty($fields)) {
                    $group['subConfig'] = $fields;
                }

                $groups[] = $group;
            }

            if (false === empty($groups)) {
                $section['subConfig'] = $groups;
            }

            $nodes[] = $section;
        }

        $output['nodes'] = $nodes;

        return $output;
    }

    protected function _transformElement($nodeId, $config, $nodeName, $allowedNames = array())
    {
        $element = array();
        $element['nodeName'] = $nodeName;
        if (false === empty($nodeId)) {
            $element['@attributes']['id'] = $nodeId;
        }
        $attributes = isset($config['@attributes']) ? $config['@attributes'] : array();
        $element = $this->_transformAttributes($attributes, $element);

        if (false === empty($attributes)) {
            if (false == is_array($attributes)) {
                var_dump($config);
            }
            unset($config['@attributes']);
        }

        $element = $this->_transformNodes($config, $element, $allowedNames);
        return $element;
    }

    /**
     * Transform attributes
     *
     * @param array $attributes
     * @param array $element
     * @return array
     */
    protected function _transformAttributes($attributes, $element)
    {
        foreach ($attributes as $attributeName => $attributeValue) {
            $element['@attributes'][$this->_getAttributeName($attributeName)] = $attributeValue;
        }
        return $element;
    }

    protected function _needMoveToAttribute($key)
    {
        return isset($this->_attributeMaps[$key]);
    }

    protected function _transformNodes($config, $element, $allowedNames = array())
    {
        $element['parameters'] = array();
        foreach($config as $nodeName => $nodeValue) {
            if ($this->_needMoveToAttribute($nodeName)) {
                $element['@attributes'][$this->_getAttributeName($nodeName)] = $nodeValue;
                continue;
            }

            $node = array();
            if (false === empty($allowedNames) && false == in_array($nodeName, $allowedNames)) {
                $node['@attributes'] = array(
                    'type' => $nodeName
                );
                $nodeName = 'attribute';
            }

            $node['name'] = $nodeName;
            if (is_array($nodeValue)) {
                if ($nodeName === 'depends') {
                    throw new Exception('Need to be fixed');

                } else {
                    foreach ($nodeValue as $elementName => $elementConfig) {
                        $node['subConfig'][] = $this->_transformElement(null, $elementConfig, $elementName);
                    }
                }

            } else {
                $node['value'] = $nodeValue;
            }

            $element['parameters'][] = $node;
        }

        return $element;
    }

    /**
     * Get attribute name
     *
     * @param string $key
     * @return mixed
     */
    protected function _getAttributeName($key)
    {
        return isset($this->_attributeMaps[$key]) ? $this->_attributeMaps[$key] : $key;
    }


}
