<?php
/**
 * This file is part of the Trilateration package.
 * Created at 24/10/2015, 21:14
 *
 * @author Paulo Dias <pdias@tripadvisor.com>
 * @file Trilateration.php
 */

namespace Prbdias\Trilateration;


class Trilateration
{
    private $earthRadius;

    /**
     * @var Circle
     * @access private
     */
    private $circleA;

    /**
     * @var Circle
     * @access private
     */
    private $circleB;

    /**
     * @var Circle
     * @access private
     */
    private $circleC;

    /**
     * @var bool
     * @access private
     */
    private $inMiles;

    /**
     * Trilateration constructor.
     */
    public function __construct()
    {
        //Assuming elevation as 0
        $this->earthRadius = 6371;
    }

    /**
     * Define if the measurements should be in miles
     *
     * @param $inMiles
     */
    public function setMiles($inMiles)
    {
        $this->inMiles = $inMiles;
    }


    private function getKms($distance)
    {
        return $this->inMiles?($distance * 1.609344):$distance;
    }

    /**
     * Set point one
     *
     * @param $lat
     * @param $lng
     * @param $distance
     */
    public function setPoint1($lat, $lng, $distance)
    {
        $this->circleA = new Circle($lat, $lng, $this->getKms($distance));
    }

    /**
     * Set point two
     *
     * @param $lat
     * @param $lng
     * @param $distance
     */
    public function setPoint2($lat, $lng, $distance)
    {
        $this->circleB = new Circle($lat, $lng, $this->getKms($distance));
    }

    /**
     * Set point three
     *
     * @param $lat
     * @param $lng
     * @param $distance
     */
    public function setPoint3($lat, $lng, $distance)
    {
        $this->circleC = new Circle($lat, $lng, $this->getKms($distance));
    }

    /**
     * Convert geodetic lat/lng to Earth Centered Rotational xyz
     *
     * @param Circle $circle
     * @return Vector
     */
    private function getECRVector(Circle $circle)
    {
        $x = $this->earthRadius *(cos(deg2rad($circle->getLat())) * cos(deg2rad($circle->getLng())));
        $y = $this->earthRadius *(cos(deg2rad($circle->getLat())) * sin(deg2rad($circle->getLng())));
        $z = $this->earthRadius *(sin(deg2rad($circle->getLat())));

        return new Vector(array($x, $y, $z));
    }

    public function getIntersectionPoint()
    {
        if (!$this->circleA || !$this->circleB || !$this->circleC) {
            return false;
        }


        $vectorP1 = $this->getECRVector($this->circleA);
        $vectorP2 = $this->getECRVector($this->circleB);
        $vectorP3 = $this->getECRVector($this->circleC);

        #from wikipedia: http://en.wikipedia.org/wiki/Trilateration
        #transform to get circle 1 at origin
        #transform to get circle 2 on x axis

        // CALC EX
        $l = $vectorP2->subtract($vectorP1);

        $norm = new Vector(array_fill(0, 3, $l->length()));
        $d = $norm;
        $ex = $l->divide($norm);

        // CALC i
        $P3minusP1 = $vectorP3->subtract($vectorP1);
        $i = $ex->dotProduct($P3minusP1);

        // CALC EY
        $iex = $ex->multiplyByScalar($i);
        $P3P1iex = $P3minusP1->subtract($iex);
        $l = $P3P1iex;
        $norm = new Vector(array_fill(0, 3, $l->length()));
        $ey = $P3P1iex->divide($norm);

        // CALC EZ
        $ez = $ex->crossProduct($ey);

        // CALC D
        $d = $d->getElement(0) ;

        // CALC J
        $j = $ey->dotProduct($P3minusP1);

        #from wikipedia
        #plug and chug using above values
        $x = (pow($this->circleA->getRadius(), 2) - pow($this->circleB->getRadius(), 2) + pow($d, 2))/(2*$d);
        $y = ((pow($this->circleA->getRadius(), 2) - pow($this->circleC->getRadius(), 2) + pow($i, 2) + pow($j, 2))/(2*$j)) - (($i/$j)*$x);

        # only one case shown here
        $z = sqrt( pow($this->circleA->getRadius(),2) - pow($x,2) - pow($y,2) );

        #triPt is an array with ECEF x,y,z of trilateration point
        $xex = $ex->multiplyByScalar($x);
        $yey = $ey->multiplyByScalar($y);
        $zez = $ez->multiplyByScalar($z);

        // CALC $triPt = $P1vector + $xex + $yey + $zez;
        $triPt = $vectorP1
            ->add($xex)
            ->add($yey)
            ->add($zez);

        $triPtX = $triPt->getElement(0);
        $triPtY = $triPt->getElement(1);
        $triPtZ = $triPt->getElement(2);

        #convert back to lat/long from ECEF
        #convert to degrees
        $lat = rad2deg(asin($triPtZ / $this->earthRadius));
        $lng = rad2deg(atan2($triPtY,$triPtX));

        return compact('lat', 'lng');
    }
}