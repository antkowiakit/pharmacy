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
     *  requirements={
     *      {
     *          "name"="range",
     *          "dataType"="float",
     *          "description"="Range from coordinate."
     *      },
     *     {
     *          "name"="lat",
     *          "dataType"="float",
     *          "description"="Latitude."
     *      },
     *     {
     *          "name"="lng",
     *          "dataType"="float",
     *          "description"="Longitude."
     *      },
     *  },
     *   section = "pharmacy"
     * )
     *
     * @Rest\View()
     * @Rest\Get("/{lat}/{lng}/{range}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($lat, $lng, $range)
    {
        $pharmacyInput = new PharmacyInput($lat, $lng, $range);
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
     * @Rest\Post("/import")
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
