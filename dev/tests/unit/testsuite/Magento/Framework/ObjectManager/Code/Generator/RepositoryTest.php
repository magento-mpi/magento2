<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Code\Generator;

use Magento\Framework\Api\Code\Generator\EntityChildTestAbstract;
use Magento\Framework\Code\Generator\Io;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class RepositoryTest
 */
class RepositoryTest extends EntityChildTestAbstract
{
    const SOURCE_CLASS_NAME = 'Magento\Framework\ObjectManager\Code\Generator\Sample';
    const RESULT_CLASS_NAME = 'Magento\Framework\ObjectManager\Code\Generator\SampleRepository';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\ObjectManager\Code\Generator\Repository';
    const OUTPUT_FILE_NAME = 'SampleConverter.php';

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
