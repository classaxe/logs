<?php
namespace App\Models;
// https://github.com/meetmatt/SmallestEnclosingCircle/blob/master/src/Point.php

class Point {
    private $x;
    private $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }


    public function getDistance(self $point) {
        return hypot($this->x - $point->getX(), $this->y - $point->getY());
    }


    public function subtract(self $point) {
        return new self($this->x - $point->getX(), $this->y - $point->getY());
    }

    public function cross(self $point) {
        return $this->x * $point->getY() - $this->y * $point->getX();
    }

    public function __toString() {
        return sprintf('Point(%f, %f)', $this->x, $this->y);
    }
}
