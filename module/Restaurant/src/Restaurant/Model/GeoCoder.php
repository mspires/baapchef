<?php
namespace Restaurant\Model;

use Zend\Http\Client;

//http://www.drzycimski.com/programming/zend-framework/google-maps-geocoding-api-with-json-and-php/
class GeoCoder
{
    /**
     * @class vars
     */
    
    // Google´s geocode URL
    public $url = 'http://maps.google.com/maps/api/geocode/json?';
    
    // Params for request
    public $sensor       = "false"; // REQUIRED FOR REQUEST!
    public $language     = "en";
    
    // Class vars
    public $response     = '';
    public $country_long = '';
    public $country_short= '';
    public $region_long  = '';
    public $region_short = '';
    public $city         = '';
    public $address      = '';
    public $lat          = '';
    public $lng          = '';
    public $location_type= '';
    
    /**
     * Constructor
     *
     * @param mixed $config
     * @return void
     */
    function __construct($config = null)
    {}
    
    /**
     * Forward search: string must be an address
     *
     * @param string $address
     * @return obj $response
     */
    public function forwardSearch($address)
    {
        return $this->_sendRequest("address=" . urlencode(stripslashes($address)));
    } // end forward
    
    /**
     * Reverse search: string must be latitude and longitude
     *
     * @param float $lat
     * @param float $lng
     * @return obj $response
     */
    public function reverseSearch($lat, $lng)
    {
        return $this->_sendRequest("latlng=" . (float) $lat . ',' . (float) $lng);
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
        $country = $this->searchAddressComponents("country");
        $this->country_long    = $country->long_name;
        $this->country_short    = $country->short_name;
        $region = $this->searchAddressComponents("administrative_area_level_1");
        $this->region_long = $region->long_name;
        $this->region_short    = $region->short_name;
        $city = $this->searchAddressComponents("locality");
        $this->city    = $city->short_name;
        $this->address = $this->response->results[0]->formatted_address;
        $this->lat = $this->response->results[0]->geometry->location->lat;
        $this->lng = $this->response->results[0]->geometry->location->lng;
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
        $client = new Client();
        $client->setUri($this->url . $search . '&language=' . strtolower($this->language) . '&sensor=' . strtolower($this->sensor));
        //$client->setConfig(array(
        //     'maxredirects' => 0,
        //     'timeout'      => 30));
        $client->setHeaders(array(
             'Accept-encoding' => 'json',
             'X-Powered-By' => 'Zend Framework GEOCMS by Joerg Drzycimski'));
        $response = $client->getRequest();
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