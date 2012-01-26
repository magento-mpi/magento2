<?php

/**
 * Base class for all API collection resources
 */
abstract class Mage_Api2_Model_Resource_Instance extends Mage_Api2_Model_Resource
{
    /**
     * Internal "instance" resource model dispatch
     */
    public function dispatch()
    {
        $operation = $this->getRequest()->getOperation();

        switch ($operation) {
            //not exist for this kind of resource
            case self::OPERATION_CREATE:
                $this->create(array());
                break;

            case self::OPERATION_UPDATE:
                $data = $this->getRequest()->getBodyParams();
                $filtered = $this->getFilter()->in($data);
                $this->$operation($filtered);
                break;

            case self::OPERATION_RETRIEVE:
                //TODO how we process &include, what attributes we show by default, all allowed, all static?
                $result = $this->retrieve();
                $filtered = $this->getFilter()->out($result);
                $this->render($filtered);
                break;

            case self::OPERATION_DELETE:
                $this->delete();
                break;
        }
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @return array
     */
    protected function retrieve()
    {
        $this->critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Create method not allowed for this type of resource
     *
     * @param array $data
     */
    final protected function create(array $data)
    {
        $this->critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function update(array $data)
    {
        $this->critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     */
    protected function delete()
    {
        $this->critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }
}
