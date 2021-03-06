<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Adapter\Solr;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
    }

    /**
     * Check Sku processing by getSearchEngineFieldName method with sort target
     */
    public function testGetSearchEngineFieldName()
    {
        $sku = new \Magento\Framework\Object(['attribute_code' => 'sku']);
        /** @var $model \Magento\Solr\Model\Adapter\Solr\AbstractSolr */
        $model = $this->getMockForAbstractClass('Magento\Solr\Model\Adapter\Solr\AbstractSolr', [], '', false);
        $this->assertEquals('sku', $model->getSearchEngineFieldName($sku, 'sku'));
        $this->assertEquals('attr_sort_sku', $model->getSearchEngineFieldName($sku, 'sort'));
    }
}
