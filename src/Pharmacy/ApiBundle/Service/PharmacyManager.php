<?php
/**
 * Created by PhpStorm.
 * User: patrykantkowiak
 * Date: 24.10.2015
 * Time: 23:02
 */
namespace Pharmacy\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Pharmacy\ApiBundle\Entity\Pharmacy;
use Pharmacy\ApiBundle\Entity\PharmacyInput;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PharmacyManager {
    private $em;
    private $pharmacyRepository;
    private $validator;

    public function __construct(EntityManager $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->pharmacyRepository = $em->getRepository('PharmacyApiBundle:Pharmacy');
        $this->validator = $validator;
    }

    public function getPharmacyInRange(PharmacyInput $pharmacyInput)
    {
        $results = $this->pharmacyRepository->createQueryBuilder('p')
            ->select('p')
            ->addSelect(
                '( 6368000 * acos(cos(radians(' . $pharmacyInput->getLat() . '))' .
                '* cos( radians( p.lat ) )' .
                '* cos( radians( p.lng )' .
                '- radians(' . $pharmacyInput->getLng() . ') )' .
                '+ sin( radians(' . $pharmacyInput->getLat() . ') )' .
                '* sin( radians( p.lat ) ) ) ) AS distance'
            )
            ->having('distance < :distance')
            ->setParameter('distance', $pharmacyInput->getRange())
            ->orderBy('distance', 'ASC')
            ->getQuery()
            ->getResult();

        $pharmacys = [];
        foreach ($results as $result) {
            $result[0]->setDistance(floatval($result['distance']));
            $pharmacys[] = $result[0];
        }

        return $pharmacys;
    }

    public function createPharmacy($name, $address, $post_code, $city, $country, $lat, $lng)
    {
        $pharmacy = new Pharmacy();
        $pharmacy->setName(trim($name));
        $pharmacy->setAddress(trim($address));
        $pharmacy->setPostCode(trim($post_code));
        $pharmacy->setCity(trim($city));
        $pharmacy->setCountry(trim($country));
        $pharmacy->setLat(floatval($lat));
        $pharmacy->setLng(floatval($lng));

        return $this->save($pharmacy);
    }

    public function importPharmacy($file)
    {
        $connection = $this->em->getConnection();
        $platform  = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('pharmacy'));


        foreach ($this->csvToArray($file) as $pharmacy) {
            $this->createPharmacy($pharmacy['name'], $pharmacy['address'], $pharmacy['code'], $pharmacy['city'], $pharmacy['country'], $pharmacy['lat'], $pharmacy['lng']);
        }
    }

    public function save($e)
    {
        $validator = $this->validator->validate($e);
        if (0 === count($validator)) {
            $this->em->persist($e);
            $this->em->flush();
            return $e;
        } else {
            foreach ($validator as $v) {
                throw new HttpException(400, $v->getPropertyPath() . '-' . $v->getMessage());
            }
            return false;
        }
    }

    private function csvToArray($filename='', $delimiter=';')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
}