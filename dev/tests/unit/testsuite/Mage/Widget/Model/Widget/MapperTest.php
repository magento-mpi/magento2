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
    private $_simpleTarget;

    /** @var array */
    private $_simpleSource;

    public function setUp()
    {
        $this->_simpleSource = array(
            'widget' => array(
                array(
                    '@' => array(
                        'id'                    => 'cms_page_link',
                        'class'                 => 'Mage_Cms_Block_Widget_Page_Link',
                        'translate'             => 'label description',
                        'module'                => 'Mage_Cms',
                        'is_email_compatible'   => 'true',
                        'placeholder_image'     => 'Mage_Cms::images/widget_page_link.gif',
                    ),
                    'label'         => array('CMS Page Link'),
                    'description'   => array('Link to a CMS Page'),
                    'parameter'     => array(
                        array(
                            '@'     => array(
                                'name'       => 'page_id',
                                'type'       => 'value_renderer',
                                'visible'    => 'true',
                                'required'   => 'true',
                                'translate'  => 'label',
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
                                                    'open'  => array('Select Page...'),
                                                ),
                                            ),
                                        ),
                                    ),
                                )
                            ),
                        ),
                        array(
                            '@' => array(
                                'name'      => 'anchor_text',
                                'type'      => 'text',
                                'translate' => 'label description',
                                'visible'   => 'true',
                            ),
                            'label' => array('Anchor Custom Text'),
                            'description' => array('If empty, the Page Title will be used'),
                        ),
                    ),
                ),

            ),
        );

        $this->_simpleTarget = array(
            'cms_page_link' => array(
                '@' => array(
                    'type'      => 'Mage_Cms_Block_Widget_Page_Link',
                    'translate' => 'name description', // TODO do we need to bother validating name vs label here?
                    'module'    => 'Mage_Cms',
                ),
                'name' => 'CMS Page Link',
                'description' => 'Link to a CMS Page',
                'is_email_compatible' => 'true',
                'placeholder_image' => 'Mage_Cms::images/widget_page_link.gif',
                'parameters' => array(
                    'page_id' => array(
                        '@' => array(
                            'type'      => 'complex',
                            'translate' => 'label',
                        ),
                        'type' => 'label',  // TODO do we need to re-create this?
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
                    ),
                ),
            ),
        );

        $this->_xmlMapper = new Mage_Widget_Model_Widget_Mapper();
    }

    public function testMerge()
    {
        $result = $this->_xmlMapper->map($this->_simpleSource);

        $this->assertEquals($this->_simpleTarget, $result);
    }
}