<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = include __DIR__ . '/../_files/search_request_config.php';
        $request = reset($config);
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(
                'Magento\Framework\Search\Request\Mapper',
                [
                    'queries' => $request['queries'],
                    'rootQueryName' => 'suggested_search_container',
                    'filters' => $request['filters'],
                ]
            );
        $this->assertInstanceOf(
            '\Magento\Framework\Search\Request\QueryInterface',
            $mapper->getRootQuery()
        );
    }
}
