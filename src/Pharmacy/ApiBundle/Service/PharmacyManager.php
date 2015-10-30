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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PharmacyManager
{
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

    /** @var UploadedFile $file */
    public function importPharmacy($file)
    {
        if ($file->getMimeType() != 'text/plain') {
            throw new HttpException(400, 'Invalid file type.');
        }

        $this->em->getConnection()->beginTransaction();
        $pharmacys = $this->pharmacyRepository->findAll();
        foreach ($pharmacys as $pharmacy) {
            $this->remove($pharmacy);
        }

        try {
            foreach ($this->csvToArray($file) as $pharmacy) {
                $pharmacy = $this->createPharmacy(
                    $pharmacy['name'],
                    $pharmacy['address'],
                    $pharmacy['code'],
                    $pharmacy['city'],
                    $pharmacy['country'],
                    $pharmacy['lat'],
                    $pharmacy['lng']
                );
                $this->save($pharmacy);
                $this->em->clear();
            }
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            throw $e;
        }

        $this->em->getConnection()->commit();
    }

    public function remove($e)
    {
        $this->em->remove($e);
        $this->em->flush();
    }

    private function csvToArray($filename = '', $delimiter = ';')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function createPharmacy($name, $address, $post_code, $city, $country, $lat, $lng)
    {
        $pharmacy = new Pharmacy();
        $pharmacy->setName(trim($name));
        $pharmacy->setAddress(trim($address));
        $pharmacy->setPostCode(trim($post_code));
        $pharmacy->setCity(trim($city));
        $pharmacy->setCountry(trim($country));
        $pharmacy->setLat($this->validateLatCoordinate(floatval($lat)));
        $pharmacy->setLng($this->validateLngCoordinate(floatval($lng)));

        return $pharmacy;
    }

    private function validateLatCoordinate($coordinate)
    {
        if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $coordinate)) {
            throw new HttpException(400, 'Bad latitude coordinate.');
        }

        return $coordinate;
    }

    private function validateLngCoordinate($coordinate)
    {
        if (!preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $coordinate)) {
            throw new HttpException(400, 'Bad longitude coordinate.');
        }

        return $coordinate;
    }

    public function validate($e)
    {
        $validator = $this->validator->validate($e);
        if (0 === count($validator)) {
            $this->save($e);

            return $e;
        } else {
            foreach ($validator as $v) {
                throw new HttpException(400, $v->getPropertyPath() . '-' . $v->getMessage());
            }

            return false;
        }
    }

    public function save($e)
    {
        $this->em->persist($e);
        $this->em->flush();
    }
}