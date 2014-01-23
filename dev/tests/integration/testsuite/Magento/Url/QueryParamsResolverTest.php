<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

class QueryParamsResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Url\QueryParamsResolver
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Url\QueryParamsResolver');
    }

    public function testSetGetPurgeQueryParams()
    {
        $params = array('one' => 1, 'two' => 2);
        $this->_model->setQueryParams($params);
        $this->assertEquals($params, $this->_model->getQueryParams());

        $this->_model->purgeQueryParams();
        $this->assertEquals(array(), $this->_model->getQueryParams());
    }
}
