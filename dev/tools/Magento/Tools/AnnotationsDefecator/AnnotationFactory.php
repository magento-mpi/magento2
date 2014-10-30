<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator;

class AnnotationFactory
{
    /**
     * Creates annotation
     *
     * @param \ArrayIterator $iterator
     * @return Annotation
     */
    public function create(\ArrayIterator $iterator)
    {
        $annotation = new Annotation;

        /** @var FileItemI $line */
        while ($iterator->valid()) {
            $annotation->addLine(new Line($iterator->current(), $iterator->key()));
            $iterator->next();

            if (Annotation::isAnnotationWrapper($iterator->current())) {
                $annotation->addLine(new Line($iterator->current(), $iterator->key()));
                //$iterator->next();
                break;
            }
        }

        return $annotation;
    }
}
