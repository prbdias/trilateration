<?php
/**
 * This file is part of the Trilateration package.
 * Created at 24/10/2015, 21:58
 *
 * @author Paulo Dias <pdias@tripadvisor.com>
 * @file Vector.php
 */

namespace Prbdias\Trilateration;


class Vector
{
    /** @var array[float] The elements of the vector. */
    protected $elements;

    /**
     * Initialize the vector with its elements.
     *
     * @param array[float] $elements The elements of the vector.
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * Creates a null/zero-length vector of the given dimension.
     *
     * @param int $dimension The dimension of the vector to create.  Must be at least 0.
     * @return Vector The zero-length vector for the given dimension.
     * @throws \Exception if the dimension is less than zero.
     */
    public function nullVector($dimension)
    {
        if ($dimension < 0) {
            throw new \Exception('Dimension must be zero or greater');
        }

        if ($dimension === 0) {
            return new Vector(array());
        }

        return new Vector(array_fill(0, $dimension, 0));
    }

    /**
     * Get the elements of the vector.
     *
     * @return array[float] The elements of the vector.
     */
    public function getData()
    {
        return $this->elements;
    }

    public function getElement($position)
    {
        return (float)$this->elements[$position];
    }

    /**
     * Get the dimension/cardinality of the vector.
     *
     * @return int The dimension/cardinality of the vector.
     */
    public function dimension()
    {
        return count($this->getData());
    }

    /**
     * Returns the length of the vector.
     *
     * @return float The length/magnitude of the vector.
     */
    public function length()
    {
        $sumOfSquares = 0;
        foreach ($this->getData() as $component) {
            $sumOfSquares += pow($component, 2);
        }

        return sqrt($sumOfSquares);
    }

    /**
     * Check whether the given vector is the same as this vector.
     *
     * @param Vector $b The vector to check for equality.
     * @return bool True if the vectors are equal and false otherwise.
     */
    public function isEqual(Vector $b)
    {
        return $this->getData() === $b->getData();
    }

    /**
     * Checks whether the two vectors are of the same dimension.
     *
     * @param Vector $b The vector to check against.
     * @return bool True if the vectors are of the same dimension, false otherwise.
     */
    public function isSameDimension(Vector $b)
    {
        return $this->dimension() === $b->dimension();
    }

    /**
     * Checks whether the two vectors are of the same vector space.
     *
     * @param Vector $b The vector to check against.
     * @return bool True if the vectors are the same vector space, false otherwise.
     */
    public function isSameVectorSpace(Vector $b)
    {
        return array_keys($this->getData()) === array_keys($b->getData());
    }

    /**
     * Adds two vectors together.
     *
     * @param Vector $b The vector to add.
     * @return Vector The sum of the two vectors.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function add(Vector $b)
    {
        $this->checkVectorSpace($b);

        $bComponents = $b->getData();
        $sum = array();
        foreach ($this->getData() as $i => $component) {
            $sum[$i] = $component + $bComponents[$i];
        }

        return new Vector($sum);
    }

    /**
     * Divides two vectors.
     *
     * @param Vector $b The vector to add.
     * @return Vector The sum of the two vectors.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function divide(Vector $b)
    {
        $this->checkVectorSpace($b);

        $bComponents = $b->getData();
        $divide = array();
        foreach ($this->getData() as $i => $component) {
            if ($bComponents[$i] == 0) {
                throw new \Exception('Cannot divide by zero');
            }
            $divide[$i] = $component / $bComponents[$i];
        }

        return new Vector($divide);
    }

    /**
     * Subtracts the given vector from this vector.
     *
     * @param Vector $b The vector to subtract from this vector.
     * @return Vector The difference of the two vectors.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function subtract(Vector $b)
    {
        return $this->add($b->multiplyByScalar(-1));
    }

    /**
     * Computes the dot product, or scalar product, of two vectors.
     *
     * @param Vector $b The vector to multiply with.
     * @return int|float The dot product of the two vectors.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function dotProduct(Vector $b)
    {
        $this->checkVectorSpace($b);

        $bComponents = $b->getData();
        $product = 0;
        foreach ($this->getData() as $i => $component) {
            $product += $component * $bComponents[$i];
        }

        return $product;
    }

    /**
     * Computes the cross product, or vector product, of two vectors.
     *
     * @param Vector $b The vector to multiply with.
     * @return Vector The cross product of the two vectors.
     * @throws \Exception if the vectors are not 3-dimensional.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function crossProduct(Vector $b)
    {
        $this->checkVectorSpace($b);
        if ($this->dimension() !== 3) {
            throw new \Exception('Both vectors must be 3-dimensional');
        }

        $tc = $this->getData();
        $bc = $b->getData();
        list($k0, $k1, $k2) = array_keys($tc);
        $product = array(
            $k0 => $tc[$k1] * $bc[$k2] - $tc[$k2] * $bc[$k1],
            $k1 => $tc[$k2] * $bc[$k0] - $tc[$k0] * $bc[$k2],
            $k2 => $tc[$k0] * $bc[$k1] - $tc[$k1] * $bc[$k0],
        );

        return new Vector($product);
    }

    /**
     * Computes the scalar triple product of three vectors.
     *
     * @param Vector $b The second vector of the triple product.
     * @param Vector $c The third vector of the triple product.
     * @return int|float The scalar triple product of the three vectors.
     * @throws \Exception if the vectors are not 3-dimensional.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function scalarTripleProduct(Vector $b, Vector $c)
    {
        return $this->dotProduct($b->crossProduct($c));
    }

    /**
     * Computes the vector triple product of three vectors.
     *
     * @param Vector $b The second vector of the triple product.
     * @param Vector $c The third vector of the triple product.
     * @return Vector The vector triple product of the three vectors.
     * @throws \Exception if the vectors are not 3-dimensional.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function vectorTripleProduct(Vector $b, Vector $c)
    {
        return $this->crossProduct($b->crossProduct($c));
    }

    /**
     * Multiplies the vector by the given scalar.
     *
     * @param float $scalar The real number to multiply by.
     * @return Vector The result of the multiplication.
     */
    public function multiplyByScalar($scalar)
    {
        $result = array();
        foreach ($this->getData() as $i => $component) {
            $result[$i] = $component * $scalar;
        }

        return new Vector($result);
    }

    /**
     * Divides the vector by the given scalar.
     *
     * @param float $scalar The real number to divide by.
     * @return Vector The result of the division.
     * @throws \Exception if the $scalar is 0.
     */
    public function divideByScalar($scalar)
    {
        if ($scalar == 0) {
            throw new \Exception('Cannot divide by zero');
        }

        return $this->multiplyByScalar(1.0 / $scalar);
    }

    /**
     * Return the normalized vector.
     *
     * The normalized vector (or unit vector) is the vector with the same
     * direction as this vector, but with a length/magnitude of 1.
     *
     * @return Vector The normalized vector.
     * @throws \Exception if the vector length is zero.
     */
    public function normalize()
    {
        return $this->divideByScalar($this->length());
    }

    /**
     * Project the vector onto another vector.
     *
     * @param Vector $b The vector to project this vector onto.
     * @return Vector The vector projection of this vector onto $b.
     * @throws \Exception if the vector length of $b is zero.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function projectOnto(Vector $b)
    {
        $bUnit = $b->normalize();
        return $bUnit->multiplyByScalar($this->dotProduct($bUnit));
    }

    /**
     * Returns the angle between the two vectors.
     *
     * @param Vector $b The vector to compute the angle between.
     * @return float The angle between the two vectors in radians.
     * @throws \Exception if either of the vectors are zero-length.
     * @throws \Exception if the vectors are not in the same vector space.
     * @see checkVectorSpace() For Exception information.
     */
    public function angleBetween(Vector $b)
    {
        $denominator = $this->length() * $b->length();
        if ($denominator == 0) {
            throw new \Exception('Cannot divide by zero');
        }

        return acos($this->dotProduct($b) / $denominator);
    }

    /**
     * Checks that the vector spaces of the two vectors are the same.
     *
     * The vectors must be of the same dimension and have the same keys in their
     * elements.
     *
     * @access private
     * @param Vector $b The vector to check against.
     * @return void
     * @throws \Exception if the vectors are not of the same dimension.
     * @throws \Exception if the vectors' elements down have the same keys.
     */
    private function checkVectorSpace(Vector $b)
    {
        if (!$this->isSameDimension($b)) {
            throw new \Exception('The vectors must be of the same dimension');
        }

        if (!$this->isSameVectorSpace($b)) {
            throw new \Exception('The vectors\' elements must have the same keys');
        }
    }
}