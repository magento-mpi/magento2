<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget;

class TabTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $helper;

    protected function setUp()
    {
        $this->helper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param string $method
     * @param string $field
     * @param mixed $value
     * @param mixed $expected
     * @dataProvider dataProvider
     */
    public function testGetters($method, $field, $value, $expected)
    {
        /** @var \Magento\Backend\Block\Widget\Tab $object */
        $object = $this->helper->getObject(
            '\Magento\Backend\Block\Widget\Tab',
            array('data' => array($field => $value))
        );
        $this->assertEquals($expected, $object->$method());
    }

    public function dataProvider()
    {
        return array(
            'getTabLabel' => array('getTabLabel', 'label', 'test label', 'test label'),
            'getTabLabel (default)' => array('getTabLabel', 'empty', 'test label', null),

            'getTabTitle' => array('getTabTitle', 'title', 'test title', 'test title'),
            'getTabTitle (default)' => array('getTabTitle', 'empty', 'test title', null),

            'canShowTab' => array('canShowTab', 'can_show', false, false),
            'canShowTab (default)' => array('canShowTab', 'empty', false, true),

            'isHidden' => array('isHidden', 'is_hidden', true, true),
            'isHidden (default)' => array('isHidden', 'empty', true, false),

            'getTabClass' => array('getTabClass', 'class', 'test classes', 'test classes'),
            'getTabClass (default)' => array('getTabClass', 'empty', 'test classes', null),

            'getTabUrl' => array('getTabUrl', 'url', 'test url', 'test url'),
            'getTabUrl (default)' => array('getTabUrl', 'empty', 'test url', '#'),
        );
    }
}
