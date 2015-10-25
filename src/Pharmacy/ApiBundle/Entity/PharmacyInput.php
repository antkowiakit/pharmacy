<?php
/**
 * Created by PhpStorm.
 * User: patrykantkowiak
 * Date: 24.10.2015
 * Time: 22:24
 */
namespace Pharmacy\ApiBundle\Entity;

final class PharmacyInput
{
    private $range;
    private $lat;
    private $lng;

    public function __construct($lat, $lng, $range)
    {
        $this->lat = floatval($lat);
        $this->lng = floatval($lng);
        $this->range = intval($range);
    }

    /**
     * @return mixed
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }
}