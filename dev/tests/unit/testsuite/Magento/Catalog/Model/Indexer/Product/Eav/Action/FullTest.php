<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav\Action;

class FullTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithAdapterErrorThrowsException()
    {
        $eavDecimalFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Indexer\Eav\DecimalFactory',
            array('create'),
            array(),
            '',
            false
        );
        $eavSourceFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Indexer\Eav\SourceFactory',
            array('create'),
            array(),
            '',
            false
        );

        $exceptionMessage = 'exception message';
        $exception = new \Exception($exceptionMessage);

        $eavDecimalFactory->expects($this->once())
            ->method('create')
            ->will($this->throwException($exception));

        $model = new \Magento\Catalog\Model\Indexer\Product\Eav\Action\Full(
            $eavDecimalFactory,
            $eavSourceFactory
        );

        $this->setExpectedException('\Magento\Catalog\Exception', $exceptionMessage);

        $model->execute();
    }
}
