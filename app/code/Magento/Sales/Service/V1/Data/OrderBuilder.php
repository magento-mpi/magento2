<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 7/14/14
 * Time: 6:30 PM
 */

namespace Magento\Sales\Service\V1\Data;


class OrderBuilder {

    private $objectManager;

    private $data;

    public function __construct(\Magento\Framework\App\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function setId($id)
    {
        $this->data['id'] = $id;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function create()
    {
        return $this->objectManager->create('Magento\Sales\Service\V1\Data\Order', ['data' => $this->data]);
    }
} 