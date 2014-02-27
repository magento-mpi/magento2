<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Factory\Fixture;

/**
 * A constructor with 2 dependencies: one injectable, another scalar
 */
class Two
{
    /**
     * @var OneScalar
     */
    private $one;

    /**
     * @var string
     */
    private $baz;

    /**
     * @param OneScalar $one
     * @param string $baz
     */
    public function __construct(OneScalar $one, $baz = 'optional')
    {
        $this->one = $one;
        $this->baz = $baz;
    }

    /**
     * @return OneScalar
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @return string
     */
    public function getBaz()
    {
        return $this->baz;
    }
} 
