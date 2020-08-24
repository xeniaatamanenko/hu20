<?php
 /**
  * for use in lending uncomment block with action
  * if you can to enable logging you need change parameter logging to true in method addOrder
  * example: $apiClient->addOrder(array_merge($_POST,$queryString),true);
  *
  */


 //Add order
 $apiClient = new CtrApiClient();
 parse_str($_SERVER['QUERY_STRING'],$queryString);
 $response = $apiClient->addOrder(array_merge($_POST,$queryString),false);
 if((int)$response['result']['success']){
     header("Location: success.html");
 };
?>

<?php

 /**
  * Class CtrApiClient wrapper for api ctr.ru
  * For detailed iformation about api
  * and methods please visit http://ctr.ru/api.php
  */
class CtrApiClient {
    const GET = 1;
    const POST = 2;

 /**
  * CTR Api methods
  * @var array
  */
    private $allowmethods = array(
        'get_landings' => array('method' => self::GET),
        'get_link' => array('method' => self::GET),
        'get_user_balance' => array('method' => self::GET),
        'get_offers' => array('method' => self::GET),
        'get_sites' => array('method' => self::GET),
        'get_order_status' => array('method' => self::GET),
        'get_order_status_with_comment' => array('method' => self::GET),
        'get_delivery_status' => array('method' => self::GET),
        'order_add' => array('method' => self::POST),
    );
 /**
  * User api key
  * @var string
  */
    protected $apiKey = '2460648b818eae5c8f37ccb3f74be44b';
    protected $lead = '6963';
    private $apiUrl = 'https://thank-you.pro/api.php';

 /**
  * Get landings from api
  * @param $site_id - Required parameter
  * @param null $pr_id
  * @return mixed
  */
    public function getLandings($site_id, $pr_id = null){

        return $this->processCurlRequest('get_landings',array('site_id'=>(int)$site_id,'pr_id'=>(int)$pr_id));
    }

 /**
  * Generate traf link
  * @param $siteId - Required parameter
  * @param null $pr_id
  * @param null $traf_type
  * @param null $traf_id
  * @param null $sub_id
  */
    public function getLink($siteId, $pr_id=null, $traf_type = null, $traf_id = null, $sub_id = null){
        $params = array(
            'site_id' => (int)$siteId,
            'pr_id' => (int)$pr_id,
            'traf_type' => (int)$traf_type,
            'traf_id' => (int)$traf_id,
            'sub_id' => (int)$sub_id
        );
        $this->processCurlRequest('get_link', $params);

    }

 /**
  * Get user balance
  * @return mixed
  */
    public function getUserBalance(){

        return $this->processCurlRequest('get_user_balance');
    }

 /**
  * Get offers
  * @return mixed
  */
    public function getOffers(){

        return $this->processCurlRequest('get_offers');
    }

 /**
  * Get sites
  * @param null $offerId
  * @return mixed
  */
    public function getSites($offerId=null){
        return $this->processCurlRequest('get_sites',array('site_id'=>(int)$offerId));
    }

 /**
  * Get orders statuses
  * @param $orderIds array
  * @return mixed
  *
  * Avaliable array keys:
  * order_ids - Order ids in Ctr database
  * out_order_ids - Order ids in partner database
  *
  * Values in array -  comma separated order ids
  *
  * Maximum 1000 items
  *
  * Example:
  *
  * array('order_ids'=>'1, 4, 78, 444, 35454')
  * or
  * array('out_order_ids'=>'1, 4, 78, 444, 35454')
  */
    public function getOrderStatus($orderIds){

        return $this->processCurlRequest('get_order_status', $orderIds);
    }

 /**
  * Get orders statuses with comments
  * @param $orderIds array
  * @return mixed
  *
  * Avaliable array keys:
  * order_ids - Order ids in Ctr database
  * out_order_ids - Order ids in partner database
  *
  * Values in array -  comma separated order ids
  *
  * Maximum 1000 items
  *
  * Example:
  *
  * array('order_ids'=>'1, 4, 78, 444, 35454')
  * or
  * array('out_order_ids'=>'1, 4, 78, 444, 35454')
  */
    public function getOrderStatusWithComment($orderIds){

        return $this->processCurlRequest('get_order_status_with_comment', $orderIds);
    }

 /**
  * Get orders delivery statuses
  * @param $orderIds array
  * @return mixed
  *
  * Avaliable array keys:
  * order_ids - Order ids in Ctr database
  * out_order_ids - Order ids in partner database
  *
  * Values in array -  comma separated order ids
  *
  * Maximum 1000 items
  *
  * Example:
  *
  * array('order_ids'=>'1, 4, 78, 444, 35454')
  * or
  * array('out_order_ids'=>'1, 4, 78, 444, 35454')
  */
    public function getDeliveryStatus($orderIds){

        return $this->processCurlRequest('get_delivery_status', $orderIds);
    }

 /**
  * Add order to CTR database.
  * Required parameters: fio, phone, address, country, ip, ua
  *
  *
  *
  * @param $orderData - assoc array
  * @return mixed
  *
  * OrderData array example:
  *
  * array(
  *       'fio' => 'User fio'
  *      'phone' => 'User phone'
  *       'address' => 'User address'
  *       'site_id' =>  Site ID in CTR database
  *       'country' => 'ISO 3166-1 alpha-2 Country code'
  *       'out_order_id' => 'order id in Your database'
  *       'ip' => IP address
  *       'ua' => User Agent
  *       'referrer' => 'Http referrer'
  * )
  *
  */
    public function addOrder($orderData,$logging=false){
        $post_data = array('fio' => $orderData['fio'],
            'phone'        => $orderData['phone'],
            'address'      => ((isset($orderData['address']) && $orderData['address'] != "")?$orderData['address']:'Адрес узнать по телефону'),
            'site_id'      => (isset($orderData['site_id'])?$orderData['site_id']:(isset($orderData['site_data'])?$orderData['site_data']:$this->lead)),
            'out_order_id' => (isset($orderData['order_id'])?(int)$orderData['order_id']:time()),
            'country'      => $orderData['country'],
            'ip'           => (isset($orderData['ip'])?(int)$orderData['ip']:$_SERVER['REMOTE_ADDR']),
            'ua'           => (isset($orderData['ip'])?(int)$orderData['ua']:$_SERVER['HTTP_USER_AGENT']),
            'referer'      => (isset($orderData['referrer'])?$orderData['referrer']:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'')),
            'utm_source'   => (isset($orderData['utm_source'])?$orderData['utm_source']:''),
            'utm_content'  => (isset($orderData['utm_content'])?$orderData['utm_content']:''),
            'utm_medium'  => (isset($orderData['utm_medium'])?$orderData['utm_medium']:''),
            'utm_campaign' => (isset($orderData['utm_campaign'])?$orderData['utm_campaign']:''),
            'sub_id' => (isset($orderData['sub_id'])?$orderData['sub_id']:''),
         );

        if($logging === true){
         $this->addToLog($post_data);
        }

        return $this->processCurlRequest('order_add',$post_data);
    }

 /**
  * Process request to CTR api server
  * @param $method
  * @param array $data
  * @return mixed
  */
    protected function processCurlRequest($method, $data = array()){

        $request = $this->buildRequest($method,$data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request['url']);
        if ($request['data'] !== "") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        };
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (!curl_errno($curl)){
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200){
                echo json_encode(curl_getinfo($curl));
                curl_close($curl);
                die('Запрос завершился с ошибкой');
            }
            else{
                $response = json_decode($response,true);
                if(key_exists('error',$response)){
                    $this->processApiError($response);
                }
                curl_close($curl);
                return $response;
            }

        }

    }

 /**
  * Build request URL to api server
  * @param $method
  * @param $params
  * @return array
  */
    private function buildRequest($method, $params){
        $params = $this->processParams($params);
        $urlParams = array('key'=>$this->apiKey,'method'=>$method);
        $result = array();
        switch ($this->allowmethods[$method]['method']){
            case self::POST:
                $result['data'] = $params;
                break;
            default:
                $urlParams = array_merge($urlParams,$params);
                $result['data'] = null;
                break;
        };
        $result['url'] = $this->apiUrl.'?'.http_build_query($urlParams);
        return $result;
    }

 /**
  * Display errors and error messages
  * @param $error
  */
    private function processApiError($error){
        die($error['error']['text']);
    }

 /**
  * Check array of parameters for empty values
  * @param $params
  * @return mixed
  */
    private function processParams($params){
        foreach ($params as $key=>$value){
            if(!$value || $value == ""){
                unset($params[$key]);
            }
        }
        return $params;
    }

 /**
  * Add data into log
  *
  */
    private function addToLog($postData){
     $file = date("d-m-y").".log";
     $message = '['.date("d-m-y H:i:s").'] POST_DATA:'.json_encode($postData).' REQUEST: '. json_encode($_REQUEST)."SERVER: ".json_encode($_SERVER).PHP_EOL;
     $save_order = fopen($file, 'a+');
     fwrite($save_order, $message);
     fclose($save_order);
    }

}
