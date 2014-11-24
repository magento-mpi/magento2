<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Api\Code\Generator;

use Magento\Framework\Code\Generator\Io;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class MapperTest
 */
class MapperTest extends EntityChildTestAbstract
{
    const SOURCE_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\Sample';
    const RESULT_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\SampleMapper';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\Mapper';
    const OUTPUT_FILE_NAME = 'SampleMapper.php';

    protected function getSourceClassName()
    {
        return self::SOURCE_CLASS_NAME;
    }

    protected function getResultClassName()
    {
        return self::RESULT_CLASS_NAME;
    }

    protected function getGeneratorClassName()
    {
        return self::GENERATOR_CLASS_NAME;
    }

    protected function getOutputFileName()
    {
        return self::OUTPUT_FILE_NAME;
    }
}
