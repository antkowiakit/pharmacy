<?php
/**
 * Created by PhpStorm.
 * User: patrykantkowiak
 * Date: 24.10.2015
 * Time: 22:02
 */
namespace Pharmacy\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Pharmacy\ApiBundle\Entity\PharmacyInput;
use Symfony\Component\Validator\Constraints as Assert;

class PharmacyController extends FOSRestController
{
    /**
     * Get pharmacy's.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get pharmacy's.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   },
     *   section = "pharmacy"
     * )
     * @param ParamFetcher $paramFetcher Paramfetcher
     * @Rest\QueryParam(name="range", requirements="[0-9]+", nullable=false, allowBlank=false, strict=true, description="Range from coordinate.")
     * @Rest\QueryParam(name="lat", requirements="(\-?)([0-8]?[0-9])|(\-?)(90(\.0+)?)", nullable=false, allowBlank=false, strict=true, description="Latitude.")
     * @Rest\QueryParam(name="lng", requirements="(\-?)(1[0-7][0-9])|([0-9]?[0-9])|(\-?)(180(\.0+)?)", nullable=false, strict=true, allowBlank=false, description="Longitude.")
     *
     * @Rest\View()
     * @Rest\Get("/pharmacy")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(ParamFetcher $paramFetcher)
    {
        $pharmacyInput = new PharmacyInput($paramFetcher->get('lat'), $paramFetcher->get('lng'), $paramFetcher->get('range'));
        $pharmacs = $this->get('pharmacy.pharmacy_manager')->getPharmacyInRange($pharmacyInput);

        $view = $this->view($pharmacs, 200);

        return $this->handleView($view);
    }


    /**
     * Import pharmacy's from CSV.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Import pharmacy's from CSV",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   },
     *   section = "pharmacy"
     * )
     *
     * @Rest\FileParam(name="csv_file", description="CSV file.")
     *
     * @Rest\View()
     * @Rest\Post("pharmacy/import")
     * @param ParamFetcher $paramFetcher
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(ParamFetcher $paramFetcher)
    {
        $this->get('pharmacy.pharmacy_manager')->importPharmacy($paramFetcher->get('csv_file'));

        $view = $this->view([], 204);

        return $this->handleView($view);

    }


}
