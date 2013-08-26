<?php
/**
 * Test Mage_Widget_Model_Widget_Mapper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Widget_MapperTest extends PHPUnit_Framework_TestCase
{

    /** @var Mage_Widget_Model_Widget_Mapper */
    private $_xmlMapper;

    /** @var array */
    private $_targetArray;

    /** @var array */
    private $_sourceArray;

    public function setUp()
    {
        $this->_sourceArray = $this->_constructSource();

        $this->_targetArray = $this->_constructTarget();

        $this->_xmlMapper = new Mage_Widget_Model_Widget_Mapper();
    }

    public function testMerge()
    {
        $this->markTestIncomplete('change input structure');
        $result = $this->_xmlMapper->map($this->_sourceArray);
        $this->assertEquals($this->_targetArray, $result);
    }

    /**
     * Constructs a source array that represents the new xml converted to array using new reader.
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    private function _constructSource()
    {
        return array(
            'widget' => array(
                array(
                    '@' => array(
                        'id' => 'cms_page_link',
                        'class' => 'Mage_Cms_Block_Widget_Page_Link',
                        'translate' => 'label description',
                        'module' => 'Mage_Cms',
                        'is_email_compatible' => 'true',
                        'placeholder_image' => 'Mage_Cms::images/widget_page_link.gif',
                    ),
                    'label' => array('CMS Page Link'),
                    'description' => array('Link to a CMS Page'),
                    'parameter' => array(
                        array(
                            '@' => array(
                                'name' => 'page_id',
                                'type' => 'value_renderer',
                                'visible' => 'true',
                                'required' => 'true',
                                'translate' => 'label',
                                'sort_order' => '10',
                            ),
                            'label' => array('CMS Page'),
                            'renderer' => array(
                                array(
                                    '@' => array(
                                        'class' => 'Mage_Adminhtml_Block_Cms_Page_Widget_Chooser',
                                    ),
                                    'data' => array(
                                        array(
                                            'button' => array(
                                                array(
                                                    '@' => array(
                                                        'translate' => 'open',
                                                    ),
                                                    'open' => array('Select Page...'),
                                                ),
                                            ),
                                        ),
                                    ),
                                )
                            ),
                        ),
                        array(
                            '@' => array(
                                'name' => 'anchor_text',
                                'type' => 'text',
                                'translate' => 'label description',
                            ),
                            'label' => array('Anchor Custom Text'),
                            'description' => array('If empty, the Page Title will be used'),
                            'depends' => array(
                                array(
                                    'parameter' => array(
                                        array(
                                            '@' => array(
                                                'name' => 'show_pager',
                                                'value' => 'true',
                                            )
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            '@' => array(
                                'name' => 'template',
                                'type' => 'select',
                                'translate' => 'label',
                                'visible' => 'true',
                            ),
                            'label' => array('Template'),
                            'option' => array(
                                array(
                                    '@' => array(
                                        'name' => 'default',
                                        'value' => 'product/widget/link/link_block.phtml',
                                        'selected' => 'true',
                                        'translate' => 'label',
                                    ),
                                    'label' => array('Product Link Block Template'),
                                ),
                                array(
                                    '@' => array(
                                        'name' => 'link_inline',
                                        'value' => 'product/widget/link/link_inline.phtml',
                                        'translate' => 'label',
                                    ),
                                    'label' => array('Product Link Inline Template'),
                                ),
                            )
                        ),
                    ),
                    'container' => array(
                        array(
                            '@' => array(
                                'section' => 'left_column',
                                'name' => 'left',
                            ),
                            'template' => array(
                                array(
                                    '@' => array(
                                        'name' => 'default',
                                        'value' => 'list_default',
                                    )
                                ),
                                array(
                                    '@' => array(
                                        'name' => 'names_only',
                                        'value' => 'list_names',
                                    ),
                                ),
                            ),
                        ),
                        array(
                            '@' => array(
                                'section' => 'main_content',
                                'name' => 'content',
                            ),
                            'template' => array(
                                array(
                                    '@' => array(
                                        'name' => 'grid',
                                        'value' => 'default',
                                    )
                                ),
                                array(
                                    '@' => array(
                                        'name' => 'list',
                                        'value' => 'list',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    '@' => array(
                        'id' => 'enterprise_giftregistry_search',
                        'class' => 'Enterprise_GiftRegistry_Block_Search_Widget_Form',
                        'translate' => 'label description',
                        'module' => 'Enterprise_GiftRegistry',
                    ),
                    'label' => array('Gift Registry Search'),
                    'description' => array('Gift Registry Quick Search Form'),
                    'parameter' => array(
                        array(
                            '@' => array(
                                'name' => 'types',
                                'type' => 'multiselect',
                                'source_model' => 'Enterprise_GiftRegistry_Model_Source_Search',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Constructs a target array that represents the old xml converted to array using old reader.
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @returns array
     */
    private function _constructTarget()
    {
        return array(
            'cms_page_link' => array(
                '@' => array(
                    'type' => 'Mage_Cms_Block_Widget_Page_Link',
                    'module' => 'Mage_Cms',
                    'translate' => 'name description',
                ),
                'name' => 'CMS Page Link',
                'description' => 'Link to a CMS Page',
                'is_email_compatible' => '1',
                'placeholder_image' => 'Mage_Cms::images/widget_page_link.gif',
                'parameters' => array(
                    'page_id' => array(
                        '@' => array(
                            'type' => 'complex',
                            'translate' => 'label',
                        ),
                        'type' => 'label',
                        'helper_block' => array(
                            'type' => 'Mage_Adminhtml_Block_Cms_Page_Widget_Chooser',
                            'data' => array(
                                'button' => array(
                                    '@' => array(
                                        'translate' => 'open',
                                    ),
                                    'open' => 'Select Page...',
                                ),
                            ),
                        ),
                        'visible' => 'true',
                        'required' => 'true',
                        'sort_order' => '10',
                        'label' => 'CMS Page',
                    ),
                    'anchor_text' => array(
                        '@' => array(
                            'translate' => 'label description',
                        ),
                        'type' => 'text',
                        'visible' => 'true',
                        'label' => 'Anchor Custom Text',
                        'description' => 'If empty, the Page Title will be used',
                        'depends' => array(
                            'show_pager' => array(
                                'value' => 'true',
                            ),
                        ),
                    ),
                    'template' => array(
                        '@' => array(
                            'translate' => 'label',
                        ),
                        'type' => 'select',
                        'values' => array(
                            'default' => array(
                                '@' => array(
                                    'translate' => 'label',
                                ),
                                'value' => 'product/widget/link/link_block.phtml',
                                'label' => 'Product Link Block Template',
                            ),
                            'link_inline' => array(
                                '@' => array(
                                    'translate' => 'label',
                                ),
                                'value' => 'product/widget/link/link_inline.phtml',
                                'label' => 'Product Link Inline Template',
                            )
                        ),
                        'visible' => 'true',
                        'label' => 'Template',
                        'value' => 'product/widget/link/link_block.phtml',
                    ),
                ),
                'supported_containers' => array(
                    'left_column' => array(
                        'container_name' => 'left',
                        'template' => array(
                            'default' => 'list_default',
                            'names_only' => 'list_names',
                        ),
                    ),
                    'main_content' => array(
                        'container_name' => 'content',
                        'template' => array(
                            'grid' => 'default',
                            'list' => 'list',
                        ),
                    )
                )
            ),
            'enterprise_giftregistry_search' => array(
                '@' => array(
                    'type' => 'Enterprise_GiftRegistry_Block_Search_Widget_Form',
                    'module' => 'Enterprise_GiftRegistry',
                    'translate' => 'name description',
                ),
                'name' => 'Gift Registry Search',
                'description' => 'Gift Registry Quick Search Form',
                'parameters' => array(
                    'types' => array(
                        'type' => 'multiselect',
                        'visible' => 'true', // verify default value is injected
                        'source_model' => 'Enterprise_GiftRegistry_Model_Source_Search',
                    ),
                ),
            ),

        );
    }
}