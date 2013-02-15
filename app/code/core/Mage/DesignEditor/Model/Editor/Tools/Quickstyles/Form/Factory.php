<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * VDE area model
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Factory extends Varien_Data_Form_Factory
{
    const COLUMN_RENDERER_LAYOUT_NAME = 'design_editor_tools_design_column';

    const COLUMN_ELEMENT_RENDERER_LAYOUT_NAME = 'design_editor_tools_design_column_element';

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory
     */
    protected $_rendererFactory;

    protected $_xmlReader;

    /**
     * Constructor
     */
    public function __construct(
        Mage_Core_Model_Translate $translator,
        Varien_Object $xmlReader,
        Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory $rendererFactory
    ) {
        $this->_translator = $translator;
        $this->_rendererFactory = $rendererFactory;

        //@TODO remove xmlReader or use it
        $this->_xmlReader = $xmlReader;
    }

    /**
     * Create varien data form with provided params
     *
     * @param array $data
     * @return Varien_Data_Form
     * @throws InvalidArgumentException
     */
    public function create(array $data = array())
    {
        /** @var $form Varien_Data_Form */
        $form = parent::create($data);

        $this->addElementTypes($form);

        if (!isset($data['group'])) {
            throw new InvalidArgumentException((sprintf('Invalid controls group "%s".', $data['group'])));
        }

        $columns = $this->initColumns($form, $data['group'], $data['layout'], $data['layout_name']);
        $this->populateColumns($columns, $data['group'], $data['layout']);

        return $form;
    }

    /**
     * Add column elements to form
     *
     * @param Varien_Data_Form $form
     * @param string $group
     * @param Mage_Core_Model_Layout $layout
     * @param string $parentName
     * @return array
     */
    protected function initColumns($form, $group, $layout, $parentName)
    {
        // NOTICE: Since column renderer is shared it's impossible to render nested columns
        $columnRenderer = $this->getColumnRenderer($layout);

        $columnLeft = $form->addField('column-left-' . $group, 'column', array())->setRenderer($columnRenderer);
        $columnMiddle = $form->addField('column-middle-' . $group, 'column', array())->setRenderer($columnRenderer);
        $columnRight = $form->addField('column-right-' . $group, 'column', array())->setRenderer($columnRenderer);

        $columns = array(
            'column-left' => $columnLeft,
            'column-middle' => $columnMiddle,
            'column-right' => $columnRight
        );

        return $columns;
    }

    /**
     * Populate columns with fields
     *
     * @param array $columns
     * @param string $group
     * @param Mage_Core_Model_Layout $layout
     */
    protected function populateColumns($columns, $group, $layout)
    {
        // NOTICE: Since column element renderer is shared it's impossible to render nested column elements
        $columnElementRenderer = $this->getColumnElementRenderer($layout);

        // Backup original renderer
        $originalRenderer = Varien_Data_Form::getFieldsetElementRenderer();

        // Level 3 element renderer
        $fieldsetElementRenderer = $this->getColumnElementRenderer($layout);
        Varien_Data_Form::setFieldsetElementRenderer($fieldsetElementRenderer);

        foreach ($this->getControlsLayout($group) as $id => $positionData) {
            $control = $this->getControls($id);
            $this->setDefaultValues($control, $id);

            $htmlId = 'control-' . $id;
            $config = $this->buildElementConfig($htmlId, $positionData, $control);


            /** @var $column Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column */
            $column = $columns[$positionData['column']];
            $column->addField($htmlId, $control['type'], $config)->setRenderer($columnElementRenderer);
        }

        // Restore original renderer
        Varien_Data_Form::setFieldsetElementRenderer($originalRenderer);
    }

    protected function buildElementConfig($htmlId, $positionData, $control)
    {
        $label = $this->__($positionData['title']);

        $config = array(
            'name'  => $htmlId,
            'label' => $label,
        );
        if (isset($control['components'])) {
            $config['components'] = $control['components'];
            $config['title'] = $label;
        } else {
            //$control should contain 'default', 'selector' and 'attribute'; may contain 'options';

            $config['value'] = $control['value'];
            $config['title'] = sprintf('%s {%s: %s}',
                $control['selector'],
                $control['attribute'],
                $control['value']
            );
            if (isset($control['options'])) {
                $config['options'] =  $control['options'];
            }
        }

        return $config;
    }

    /**
     * Get column renderer
     *
     * @param Mage_Core_Model_Layout $layout
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column
     */
    protected function getColumnRenderer($layout)
    {
        /** @var $columnRenderer Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column|false */
        $columnRenderer = $layout->getBlock(self::COLUMN_RENDERER_LAYOUT_NAME);
        if (!$columnRenderer) {
            $columnRenderer = $layout->createBlock(
                'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column',
                self::COLUMN_RENDERER_LAYOUT_NAME
            );
        }

        return $columnRenderer;
    }

    /**
     * Get column element renderer
     *
     * @param Mage_Core_Model_Layout $layout
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column_Element
     */
    protected function getColumnElementRenderer($layout)
    {
        /** @var $columnElementRenderer Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column_Element|false */
        $columnElementRenderer = $layout->getBlock(self::COLUMN_ELEMENT_RENDERER_LAYOUT_NAME);
        if (!$columnElementRenderer) {
            $columnElementRenderer = $layout->createBlock(
                'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column_Element',
                self::COLUMN_ELEMENT_RENDERER_LAYOUT_NAME
            );
        }

        return $columnElementRenderer;
    }

    /**
     * Add custom element types for VDE "Tools" panel "Quick Styles" tab
     *
     * @param Varien_Data_Form $form
     */
    protected function addElementTypes($form)
    {
        $form->addType('column', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column');
    }

    /**
     * Set values recursively, if value is not defined 'default' value is used
     *
     * After this operation every simple component will contain 'value' element
     *
     * @param array $data
     * @param string $id
     * @throws Mage_Core_Exception
     */
    protected function setDefaultValues(&$data, $id)
    {
        $fixValuesIfNotBetweenOptions = true;

        if (isset($data['components'])) {
            foreach ($data['components'] as $componentId => &$component) {
                $this->setDefaultValues($component, $componentId);
            }
        } else {
            $value = $this->getControlsValue($id);
            if ($value === null) {
                $value = $data['default'];
            } else {
                $isInvalidValue = is_array($data['options']) && array_search($value, $data['options']) === false;
                if ($isInvalidValue) {
                    if ($fixValuesIfNotBetweenOptions) {
                        $value = $data['default'];
                    } else {
                        $message = sprintf('Invalid value "%s" for control "%s" while only possible options are ["%s"]',
                            $value, $id, join('", "', $data['options'])
                        );
                        throw new Mage_Core_Exception($message);
                    }
                }
            }

            $data['value'] = $value;
        }
    }

    /**
     * Translate sentence
     *
     * @return string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    protected function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), 'Mage_DesignEditor');
        array_unshift($args, $expr);
        return $this->_translator->translate($args);
    }

    /**
     * Mock data from layout.xml
     *
     * @param string $tab
     * @return array
     */
    protected function getControlsLayout($tab)
    {
        $layoutDataByTab = array(
            'header'      => array(
                'store-name'         => array(
                    'title'  => 'Store Name',
                    'used'   => 'Global Header',
                    'column' => 'column-left',
                ),
                'header-background'         => array(
                    'title'  => 'Background',
                    'used'   => 'Global Header',
                    'column' => 'column-left',
                ),
                'menu-background'    => array(
                    'title'  => 'Menu Background',
                    'used'   => 'Main Nav Drop-down BG, Language and Currency Drop-downs',
                    'column' => 'column-middle',
                ),
                'menu-links'         => array(
                    'title'  => 'Menu Links',
                    'used'   => 'Links in the drop down menu for main nav',
                    'column' => 'column-middle',
                ),
                'menu-links-hover'   => array(
                    'title'  => 'Menu Links Hover',
                    'used'   => 'Links in the drop down menu for main nav',
                    'column' => 'column-middle',
                ),
                'header-links'       => array(
                    'title'  => 'Header Links',
                    'used'   => 'Cart Icon, Header Links, Language and Currency Links, Main Nav Liks, Drop down arrow for language and currency, Drop down arrow for "More" menu',
                    'column' => 'column-right',
                ),
                'header-links-hover' => array(
                    'title'  => 'Header Links Hover',
                    'used'   => 'Header Links Hover, Cart Icon Hover, Main Nav Liks, Drop down arrow for language and currency',
                    'column' => 'column-right',
                ),

                'scroll-bar-background' => array(
                    'title'  => 'Scroll Bar Background',
                    'used'   => 'In drop-down menus in header',
                    'column' => 'column-right',
                ),
                'scroll-bar-handle' => array(
                    'title'  => 'Scroll Bar Handle',
                    'used'   => 'In drop-down menus in header',
                    'column' => 'column-right',
                ),

                'search-field' => array(
                    'title'  => 'Search Field',
                    'used'   => 'Global Header',
                    'column' => 'column-right',
                ),
                'search-field-stroke' => array(
                    'title'  => 'Search Field Stroke, Search Icon',
                    'used'   => 'Global Header',
                    'column' => 'column-right',
                ),
            ),
            'backgrounds' => array(),
            'buttons'     => array(),
        );

        return $layoutDataByTab[$tab];
    }

    /**
     * Mock data from quick-styles.xml
     *
     * @param string|null $id
     * @return array
     */
    protected function getControls($id = null)
    {
        //if we have type components defined in XML we can get rid of composite element classes

        $controlsData = array(
            // 2. less expanded, recursive
            'store-name' => array(
                'type'       => 'logo',
                'components' => array(
                    'store-name:font' => array(
                        'type'  => 'font',
                        'components' => array(
                            'store-name:font-picker' => array(
                                'type'  => 'font-picker',
                                'default'  => 'Tahoma, Geneva, sans-serif',
                                'options'  => array(
                                    'Verdana, Geneva, sans-serif',
                                    'Tahoma, Geneva, sans-serif',
                                    'Georgia, serif'
                                ),
                                'selector' => '.logo',
                                'attribute' => 'font-family',
                            ),
                            'store-name:color-picker' => array(
                                'type'  => 'color-picker',
                                'default'  => '#ccc',
                                'selector' => '.logo',
                                'attribute' => 'color',
                            ),
                        )
                    ),
                    'store-name:logo-uploader' => array(
                        'default'   => 'logo.png',
                        'selector'  => '.logo',
                        'attribute' => 'background-image',
                    ),
                )
            ),
            'header-background' => array(
                'type'       => 'background',
                'components' => array(
                    'header-background:color-picker' => array(
                        'type'      => 'color-picker',
                        'default'   => 'transparent',
                        'selector'  => '.header',
                        'attribute' => 'background-color'
                    ),
                    'header-background:background-uploader' => array(
                        'type'       => 'background-uploader',
                        'components' => array(
                            'header-background:image-uploader' => array(
                                'type'      => 'image-uploader',
                                'default'   => 'bg.gif',
                                'selector'  => '.header',
                                'attribute' => 'background-image',
                            ),
                            'header-background:tile' => array(
                                'type'      => 'checkbox',
                                'default'   => 'no-repeat',
                                'options'   => array('no-repeat', 'repeat', 'repeat-x', 'repeat-y', 'inherit'),
                                'selector'  => '.header',
                                'attribute' => 'background-repeat',
                            ),
                        )
                    ),
                )
            ),
            'menu-background' => array(
                'type'      => 'color-picker',
                'default'   => '#f8f8f8',
                'selector'  => '.menu',
                'attribute' => 'color',
            ),
            'menu-stroke' => array(
                'type'      => 'color-picker',
                'default'   => '#c2c2c2',
                'selector'  => '.menu',
                'attribute' => 'color',
            ),
            'menu-links' => array(
                'type'      => 'color-picker',
                'default'   => '#675f55',
                'selector'  => '.menu a',
                'attribute' => 'color',
            ),
            'menu-links-hover' => array(
                'type'      => 'color-picker',
                'default'   => '#f47a1F',
                'selector'  => '.menu a:hover',
                'attribute' => 'color',
            ),

            'header-links' => array(
                'type'      => 'color-picker',
                'default'   => '#837d75',
                'selector'  => '.header a',
                'attribute' => 'color',
            ),
            'header-links-hover' => array(
                'type'      => 'color-picker',
                'default'   => '#675f55',
                'selector'  => '.header a:hover, .header a:active',
                'attribute' => 'color',
            ),

            'scroll-bar-background' => array(
                'type'      => 'color-picker',
                'default'   => '#ffffff',
                'selector'  => '.scroll',
                'attribute' => 'background-color',
            ),
            'scroll-bar-handle' => array(
                'type'      => 'color-picker',
                'default'   => '#e5e5e5',
                'selector'  => '.scroll .handle',
                'attribute' => 'background-color',
            ),

            'search-field' => array(
                'type'      => 'color-picker',
                'default'   => '#ffffff',
                'selector'  => '.scroll',
                'attribute' => 'background-color',
            ),
            'search-field-stroke' => array(
                'type'      => 'color-picker',
                'default'   => '#c2c2c2',
                'selector'  => '.scroll .handle',
                'attribute' => 'background-color',
            ),





            // 2. most expanded, references
            /*'store-name' => array(
                'type'   => 'logo',
                'components' => array(
                    'store-name-font',
                    'store-name-logo-uploader'
                )
            ),
            'store-name-logo-uploader' => array(
                'type'   => 'logo-uploader',
                'default'  => 'logo.png',
                'selector' => 'body',
                'attribute' => 'background-image',
            ),
            'store-name-font' => array(
                'type'   => 'font',               //components are defined here or in composite element class
                'components' => array(
                    'store-name-font-picker',
                    'store-name-color-picker',
                )
            ),
            'store-name-font-picker' => array(
                //'type' => 'font-picker',
                'default'  => 'Tahoma',
                'options'  => array('Tahoma', 'Verdana'),
                'selector' => 'body',
                'attribute' => 'font-family',
            ),
            'store-name-color-picker' => array(
                'default'  => '#ccc',
                'selector' => 'body',
                'attribute' => 'color',
            ),*/




            // 3. most collapsed
            /*'store-name' => array(
                'type'   => 'logo',
                'components' => array(
                    'store-name-font-picker' => array(
                        'default'  => 'Tahoma',
                        'options'  => array('Tahoma', 'Verdana'),
                        'selector' => 'body',
                        'attribute' => 'font-family',
                    ),
                    'store-name-color-picker' => array(
                        'default'  => '#ccc',
                        'selector' => 'body',
                        'attribute' => 'color',
                    ),
                    'store-name-logo-uploader' => array(
                        'default'  => 'logo.png',
                        'selector' => 'body',
                        'attribute' => 'background-image',
                    ),
                )
            ),*/




            // 4. most simple
            /*'store-name-font-picker' => array(
                'default'  => 'Tahoma',
                'options'  => array('Tahoma', 'Verdana'),
                'selector' => 'body',
                'attribute' => 'font-family',
            ),
            'store-name-color-picker' => array(
                'default'  => '#ccc',
                'selector' => 'body',
                'attribute' => 'color',
            ),
            'store-name-logo-uploader' => array(
                'default'  => 'logo.png',
                'selector' => 'body',
                'attribute' => 'background-image',
            ),*/
        );

        if ($id === null) {
            $result = $controlsData;
        } else {
            $result = $controlsData[$id];
        }

        return $result;
    }

    /**
     * Mock data from view.xml
     */
    protected function getControlsValue($id)
    {
        $values = array(
            'store-name:font-picker'           => 'Georgia, serif',
            'store-name:color-picker'          => 'green',
            'store-name:logo-uploader'         => 'logo-new.png',
            'header-background:color-picker'   => 'red',
            'header-background:image-uploader' => 'header-new.png',
            'header-background:tile'           => 'repeat',
            'menu-background'                  => 'red',
            'menu-links'                       => 'red',
            'menu-links-hover'                 => 'red',
            'header-links'                     => 'red',
            'header-links-hover'               => 'red',
        );

        if (isset($values[$id])) {
            $value = $values[$id];
        } else {
            $value = null;
        }
        return $value;
    }
}
