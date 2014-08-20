<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition\Sql;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testExpression()
    {
        $expression = (new ObjectManagerHelper($this))->getObject(
            '\Magento\Rule\Model\Condition\Sql\Expression',
            ['expression' => 'SQL_EXPRESSION']
        );
        $this->assertEquals('(SQL_EXPRESSION)', (string)$expression);
    }
}
