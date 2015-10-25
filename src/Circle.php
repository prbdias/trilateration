<?php
/**
 * This file is part of the Trilateration package.
 * Created at 24/10/2015, 21:19
 *
 * @author Paulo Dias <pdias@tripadvisor.com>
 * @file Circle.php
 */

namespace Prbdias\Trilateration;


class Circle
{
    /**
     * @var float
     * @access private
     */
    private $lat;

    /**
     * @var float
     * @access private
     */
    private $lng;

    /**
     * @var float
     * @access private
     */
    private $radius;

    /**
     * Circle constructor.
     * @param float $lat
     * @param float $lng
     * @param float $radius
     */
    public function __construct($lat, $lng, $radius)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
    }

    /**
     * Get centre latitude
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Get centre longitude
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Get the radius from the centre
     *
     * @return float
     */
    public function getRadius()
    {
        return $this->radius;
    }
}