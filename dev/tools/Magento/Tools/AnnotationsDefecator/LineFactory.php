<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator;

use Magento\Tools\AnnotationsDefecator\Line\FunctionClassItem;

class LineFactory
{
    /**
     * @var AnnotationFactory
     */
    private $annotationFactory;

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->annotationFactory = new AnnotationFactory();
    }

    /**
     * Creates Line
     *
     * @param \ArrayIterator $iterator
     * @return FileItemI
     */
    public function create(\ArrayIterator $iterator)
    {
        $line = new Line($iterator->current(), $iterator->key());

        if (Annotation::isAnnotationWrapper($line->getContent())) {
            return $this->annotationFactory->create($iterator);
        }

        if (FunctionClassItem::isFunctionClassItem($line->getContent())) {
            $line = new FunctionClassItem($iterator->current(), $iterator->key());
        }

        if (Line\Property::isProperty($line->getContent())) {
            $line = new Line\Property($iterator->current(), $iterator->key());
        }

        return $line;
    }
}
