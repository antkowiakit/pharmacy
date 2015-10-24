<?php
/**
 * Created by PhpStorm.
 * User: patrykantkowiak
 * Date: 24.10.2015
 * Time: 22:24
 */
namespace Pharmacy\ApiBundle\Entity;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class PharmacyInput {
    private $range;
    private $lat;
    private $lng;

    public function __construct($lat, $lng, $range)
    {
        $this->lat = floatval($lat);
        $this->validateCoordinate($this->lat);
        $this->lng = floatval($lng);
        $this->validateCoordinate($this->lng);
        $this->range = floatval($range);
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

    private function validateCoordinate($coordinate) {
        if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?);[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $coordinate)) {
            throw new HttpException(400, 'Bad coordinate.');
        }
    }
}