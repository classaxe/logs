<?php
namespace App\Models;
// https://github.com/meetmatt/SmallestEnclosingCircle/blob/master/src/SmallestEnclosingCircle.php

class SmallestEnclosingCircle {
    /**
     * Returns the smallest circle that encloses all the given points. Runs in expected O(n) time, randomized.
     * Note: If 0 points are given, null is returned. If 1 point is given, a circle of radius 0 is returned.
     *
     * @param Point[] $points
     *
     * @return Circle|null
     */
    public static function makeCircle(array $points) {
        shuffle($points);
        /** @var Circle $circle */
        $circle = null;
        for ($i = 0, $size = count($points); $i < $size; $i++) {
            $p = $points[$i];
            if ($circle === null || !$circle->contains($p)) {
                $circle = self::makeCircleOnePoint(array_slice($points, 0, $i + 1), $p);
            }
        }
        return $circle;
    }

    /**
     * @param Point $a
     * @param Point $b
     *
     * @return Circle
     */
    public static function makeDiameter(Point $a, Point $b) {
        $c = new Point(($a->getX() + $b->getX()) / 2, ($a->getY() + $b->getY()) / 2);

        return new Circle($c, max($c->getDistance($a), $c->getDistance($b)));
    }

    /**
     * Mathematical algorithm from Wikipedia: Circumscribed circle
     *
     * @param Point $a
     * @param Point $b
     * @param Point $c
     *
     * @return Circle|null
     */
    public static function makeCircumcircle(Point $a, Point $b, Point $c) {
        $ox = (min(min($a->getX(), $b->getX()), $c->getX()) + max(min($a->getX(), $b->getX()), $c->getX())) / 2;
        $oy = (min(min($a->getY(), $b->getY()), $c->getY()) + max(min($a->getY(), $b->getY()), $c->getY())) / 2;
        $ax = $a->getX() - $ox;
        $ay = $a->getY() - $oy;
        $bx = $b->getX() - $ox;
        $by = $b->getY() - $oy;
        $cx = $c->getX() - $ox;
        $cy = $c->getY() - $oy;
        $d = ($ax * ($by - $cy) + $bx * ($cy - $ay) + $cx * ($ay - $by)) * 2;
        if ($d == 0) {
            return null;
        }
        $x = (($ax * $ax + $ay * $ay) * ($by - $cy) + ($bx * $bx + $by * $by) * ($cy - $ay) + ($cx * $cx + $cy * $cy) * ($ay - $by)) / $d;
        $y = (($ax * $ax + $ay * $ay) * ($cx - $bx) + ($bx * $bx + $by * $by) * ($ax - $cx) + ($cx * $cx + $cy * $cy) * ($bx - $ax)) / $d;
        $p = new Point($ox + $x, $oy + $y);
        $r = max(max($p->getDistance($a), $p->getDistance($b)), $p->getDistance($c));

        return new Circle($p, $r);
    }

    /**
     * @param Point[] $points
     * @param Point   $p
     *
     * @return Circle|null
     */
    private static function makeCircleOnePoint(array $points, Point $p) {
        $c = new Circle($p, 0);
        for ($i = 0, $size = count($points); $i < $size; $i++) {
            $q = $points[$i];
            if (!$c->contains($q)) {
                if ($c->getRadius() === 0) {
                    $c = self::makeDiameter($p, $q);
                } else {
                    $c = self::makeCircleTwoPoints(array_slice($points, 0, $i + 1), $p, $q);
                }
            }
        }

        return $c;
    }

    /**
     * @param Point[] $points
     * @param Point   $p
     * @param Point   $q
     *
     * @return Circle|null
     */
    private static function makeCircleTwoPoints(array $points, Point $p, Point $q) {
        $circle = self::makeDiameter($p, $q);
        /** @var Circle $left */
        $left = null;
        /** @var Circle $right */
        $right = null;

        // For each point not in the two-point circle
        $pq = $q->subtract($p);
        foreach ($points as $r) {
            if ($circle->contains($r)) {
                continue;
            }

            // Form a circumcircle and classify it on left or right side
            $cross = $pq->cross($r->subtract($p));
            $c = self::makeCircumcircle($p, $q, $r);
            if ($c === null) {
                continue;
            }

            if ($cross > 0
                && (
                    $left === null
                    ||
                    $pq->cross($c->getCenter()->subtract($p)) > $pq->cross($left->getCenter()->subtract($p))
                )
            ) {
                $left = $c;
            } elseif ($cross < 0
                && (
                    $right === null
                    ||
                    $pq->cross($c->getCenter()->subtract($p)) < $pq->cross($right->getCenter()->subtract($p))
                )
            ) {
                $right = $c;
            }
        }

        // Select which circle to return
        if ($left === null && $right === null) {
            return $circle;
        }

        if ($left === null) {
            return $right;
        }

        if ($right === null) {
            return $left;
        }

        return $left->getRadius() <= $right->getRadius() ? $left : $right;
    }
}
