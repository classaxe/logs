<?php
namespace App\Models;
// https://github.com/meetmatt/SmallestEnclosingCircle/blob/master/src/Circle.php
class Circle {
    const MULTIPLICATIVE_EPSILON = 1 + 1e-14;
    private $center;
    private $radius;

    public function __construct(Point $center, $radius) {
        $this->center = $center;
        $this->radius = $radius;
    }

    public function getCenter() {
        return $this->center;
    }

    public function getRadius() {
        return $this->radius;
    }

    public function contains(Point $point) {
        return $this->center->getDistance($point) <= $this->radius * self::MULTIPLICATIVE_EPSILON;
    }

    public function containsAll(array $points) {
        foreach ($points as $point) {
            if (!$this->contains($point)) {
                return false;
            }
        }
        return true;
    }

    public function __toString()
    {
        return sprintf('Circle(x=%f, y=%f, r=%f', $this->center->getX(), $this->center->getY(), $this->radius);
    }
}
