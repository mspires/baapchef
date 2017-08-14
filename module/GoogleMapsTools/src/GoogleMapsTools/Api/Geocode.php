<?php
namespace GoogleMapsTools\Api;

use GoogleMapsTools\Api\RemoteCall;
use GoogleMapsTools\Point;

class Geocode extends RemoteCall
{
    protected $address;
    //protected $url = 'https://maps.googleapis.com/maps/api/geocode/';
    protected $url = 'http://maps.googleapis.com/maps/api/geocode/';
    
    public function __construct($address)
    {
        $this->address = $address;
    }

    public function execute()
    {
        $params = array();
        $params['address'] = $this->address;

        parent::__execute($this->url, $params);
    }

    public function getFirstPoint()
    {
        if (is_null($this->result)) {
            $this->execute();
        }

        $location = $this->result['results'][0]['geometry']['location'];
        return new Point($location['lat'], $location['lng']);
    }
}
