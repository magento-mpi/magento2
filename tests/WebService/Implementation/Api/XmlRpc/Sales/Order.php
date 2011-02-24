<?php

class WebService_Implementation_Api_XmlRpc_Sales_Order
{

    public static function orderList($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);

        $result = true;
        foreach ($data['filters'] as $filter) {
            $filterResult = $filter['result'];
            unset($filter['result']);
            $orders = WebService_Connector_Provider::connect('XmlRpc')
                        ->getConnection()
                        ->call(
                            'call',
                            array(
                                WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                                'sales_order.list',
                                $filter)
                      );

            $obtainedResult = array();
            foreach ($orders as $id => $order){
                $obtainedResult[$id] = $order;
            }

            if(count($filterResult) == 1){
                $res = $filterResult;
                unset($filterResult);
                $filterResult[] = $res;
            }
            $keys = array_keys($filterResult[0]);
            $obtainedResult = WebService_Helper_Data::filterArray($obtainedResult, $keys);

//            echo __FILE__ . "(" . __LINE__ . "):\n";
//            var_dump($obtainedResult);
//            echo __FILE__ . "(" . __LINE__ . "):\n";
//            var_dump($filterResult);

            if($obtainedResult != $filterResult){
                $result = false;
            }
        }

        return $result;
    }

}