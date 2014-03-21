<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace ArgumentSequence;

class ContextObject implements \Magento\ObjectManager\ContextInterface
{
}
class ParentRequiredObject
{
}
class ParentOptionalObject
{
}
class ChildRequiredObject
{
}
class ChildOptionalObject
{
}
class ParentClass
{
    protected $contextObject;

    protected $parentRequiredObject;

    protected $parentRequiredScalar;

    protected $parentOptionalObject;

    protected $data;

    protected $parentOptionalScalar;

    public function __construct(
        ContextObject $contextObject,
        ParentRequiredObject $parentRequiredObject,
        array $parentRequiredScalar,
        ParentOptionalObject $parentOptionalObject = null,
        array $data = array(),
        array $parentOptionalScalar = array()
    ) {
        $this->contextObject = $contextObject;
        $this->parentRequiredObject = $parentRequiredObject;
        $this->parentOptionalScalar = $parentRequiredScalar;
        $this->parentOptionalObject = $parentOptionalObject;
        $this->data = $data;
        $this->parentOptionalScalar = $parentOptionalScalar;
    }
}
class ValidChildClass extends ParentClass
{
    protected $childRequiredObject;

    protected $childRequiredScalar;

    protected $childOptionalObject;

    protected $childOptionalScalar;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContextObject $contextObject,
        ParentRequiredObject $parentRequiredObject,
        array $parentRequiredScalar,
        ChildRequiredObject $childRequiredObject,
        array $childRequiredScalar,
        ParentOptionalObject $parentOptionalObject = null,
        array $data = array(),
        array $parentOptionalScalar = array(),
        ChildOptionalObject $childOptionalObject = null,
        array $childOptionalScalar = array()
    ) {
        $this->childRequiredObject = $childRequiredObject;
        $this->childRequiredScalar = $childRequiredScalar;
        $this->childOptionalObject = $childOptionalObject;
        $this->childOptionalScalar = $childOptionalScalar;

        parent::__construct(
            $contextObject,
            $parentRequiredObject,
            $parentRequiredScalar,
            $parentOptionalObject,
            $data,
            $parentOptionalScalar
        );
    }
}
class InvalidChildClass extends ParentClass
{
    protected $childRequiredObject;

    protected $childRequiredScalar;

    protected $childOptionalObject;

    protected $childOptionalScalar;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContextObject $contextObject,
        ChildRequiredObject $childRequiredObject,
        ParentRequiredObject $parentRequiredObject,
        array $parentRequiredScalar,
        array $childRequiredScalar,
        ParentOptionalObject $parentOptionalObject = null,
        array $data = array(),
        array $parentOptionalScalar = array(),
        ChildOptionalObject $childOptionalObject = null,
        array $childOptionalScalar = array()
    ) {
        $this->childRequiredObject = $childRequiredObject;
        $this->childRequiredScalar = $childRequiredScalar;
        $this->childOptionalObject = $childOptionalObject;
        $this->childOptionalScalar = $childOptionalScalar;

        parent::__construct(
            $contextObject,
            $parentRequiredObject,
            $parentRequiredScalar,
            $parentOptionalObject,
            $data,
            $parentOptionalScalar
        );
    }
}
