<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Encryption;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHash()
    {
        /**
         * @var \Magento\Encryption\Model
         */
        $model = new \Magento\Encryption\Model(
            $this->getMock('Magento\CryptFactory', array(), array(), '', false),
            'cryptKey'
        );
        $hash = $model->getHash('password', 'some_salt_string');

        $this->assertEquals('a42f82cf25f63f40ff85f8c9b3ff0cb4:some_salt_string', $hash);
    }
}
