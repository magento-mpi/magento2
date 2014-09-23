<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Adapter\Solr;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check Sku processing by getSearchEngineFieldName method with sort target
     */
    public function testGetSearchEngineFieldName()
    {
        $sku = new \Magento\Framework\Object(array('attribute_code' => 'sku'));
        /** @var $model \Magento\Solr\Model\Adapter\Solr\AbstractSolr */
        $model = $this->getMockForAbstractClass('Magento\Solr\Model\Adapter\Solr\AbstractSolr', array(), '', false);
        $this->assertEquals('sku', $model->getSearchEngineFieldName($sku, 'sku'));
        $this->assertEquals('attr_sort_sku', $model->getSearchEngineFieldName($sku, 'sort'));
    }
}
