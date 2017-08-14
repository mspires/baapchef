<?php
namespace Restaurant\Model;

class GeoTimeZone
{
    /**
     * @class vars
     */
    
    // Google´s geocode URL
    public $url = 'https://maps.googleapis.com/maps/api/timezone/json?';
    
    
    // Class vars
    public $response     = '';
    public $lat          = '';
    public $lng          = '';
    
    public $timestamp    = '';
    public $API_KEY      = '';
    
    /**
     * Constructor
     *
     * @param mixed $config
     * @return void
     */
    function __construct($config = null)
    {}
    
    /**
     * Reverse search: string must be latitude and longitude
     *
     * @param float $lat
     * @param float $lng
     * @return obj $response
     */
    public function Search($lat, $lng)
    {
        return $this->_sendRequest('location=' . (float) $lat . ',' . (float) $lng);
    } // end reverse
    
    /**
     * Search Address Components Object
     *
     * @param string $type
     * @return object / false
     */
    public function searchAddressComponents($type) {
        foreach($this->response->results[0]->address_components as $k=>$found){
            if(in_array($type, $found->types)){
                return $found;
            }
        }
        return false;
    }
    
    /**
     * Parse JSON default values: map object values to readable content
     *
     * @param none
     * @return none
     */
    private function _setDefaults()
    {
        $this->location_type = $this->response->results[0]->geometry->location_type;
    } // end set
    
    
    /**
     * Send Google geocoding request
     *
     * @param string $search
     * @return object response (body only)
     */
    private function _sendRequest($search)
    {
        $client = new Zend_Http_Client();
        $client->setUri($this->url . $search . '&timestamp=' . strtolower($this->timestamp) . '&key=' . $this->API_KEY);
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout'      => 30));
        $client->setHeaders(array(
            'Accept-encoding' => 'json',
            'X-Powered-By' => 'Test'));
        $response = $client->request();
        $body = $response->getBody();
        $this->response = Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);
        if ($this->response->status == "OK") {
            // set some default values for reading
            $defaults = $this->_setDefaults();
            return $this->response;
        } else {
            echo "Geocoding failed, server responded: " . $this->response->status;
            return false;
        }
    } // end request
    
}