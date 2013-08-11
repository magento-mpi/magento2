<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Block_Catalog_Product_Rss_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tag_Block_Catalog_Product_Rss_Link
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Magento_Tag_Block_Catalog_Product_Rss_Link::getLinkUrl
     * @dataProvider getUrlTestDataProvider
     *
     * @param bool $rssEnabled
     * @param int $tagId
     * @param int $existTagId
     * @param string|bool $expected
     */
    public function testGetLinkUrl($rssEnabled, $tagId, $existTagId, $expected)
    {
        $tagModelMock = $this->getMock('Magento_Tag_Model_Tag', array('getId', 'getName', 'load'), array(), '', false);
        $tagModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($existTagId));
        $tagModelMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $urlModelMock = $this->getMock('Magento_Core_Model_Url', array('getUrl'), array(), '', false);
        $urlModelMock->expects($this->any())
            ->method('getUrl')
            ->will($this->returnCallback(array($this, 'validateGetUrlCallback')));

        $data = array(
            'rss_catalog_tag_enabled' => $rssEnabled,
            'tag_id'                  => $tagId,
            'tag_model'               => $tagModelMock,
            'core_url_model'          => $urlModelMock
        );
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_Tag_Block_Catalog_Product_Rss_Link',
            array('data' => $data)
        );

        $this->assertSame($expected, $this->_model->getLinkUrl());
    }

    /**
     * @return array
     */
    public function getUrlTestDataProvider()
    {
        return array(
            'rss disabled' => array(
                '$rssEnabled' => false,
                '$tagId'      => false,
                '$existTagId' => false,
                '$expected'   => false
            ),
            'rss enabled tag_id missed' => array(
                '$rssEnabled' => true,
                '$tagId'      => false,
                '$existTagId' => false,
                '$expected'   => false
            ),
            'rss enabled tag not found' => array(
                '$rssEnabled' => true,
                '$tagId'      => 1,
                '$existTagId' => false,
                '$expected'   => false
            ),
            'rss enabled tag exists' => array(
                '$rssEnabled' => true,
                '$tagId'      => 1,
                '$existTagId' => 1,
                '$expected'   => 'rss/catalog/tag/tagName/test'
            )
        );
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    public function validateGetUrlCallback($url, $params)
    {
        $this->assertEquals('rss/catalog/tag', $url);
        $this->assertArrayHasKey('tagName', $params);
        $this->assertEquals('test', $params['tagName']);

        return $url . '/tagName/' . $params['tagName'];
    }
}
