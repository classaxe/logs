<?php

namespace App\Models;

class Image
{
    private $colors;
    private $image;
    private $images;
    private $imageDir;

    public function __construct()
    {
        $this->imageDir = public_path('images');
    }

    public function ImageColorAllocate($rgb)
    {
        $r =    hexdec(substr($rgb, 1,2));
        $g =    hexdec(substr($rgb, 3,2));
        $b =    hexdec(substr($rgb, 5,2));
        return ImageColorAllocate($this->image, $r, $g, $b);
    }

    public function ImageColorAllocateAll()
    {
        $this->colors = [
            'bg' =>         $this->ImageColorAllocate('#123456'),
            'black' =>      $this->ImageColorAllocate('#000000'),
            'white' =>      $this->ImageColorAllocate('#ffffff'),
            'darkgray' =>   $this->ImageColorAllocate('#808080'),
            'link' =>       $this->ImageColorAllocate('#0000ff')
        ];
    }

    public function ImageDrawRectangle($x1, $y1, $x2, $y2, $radius, $linecolor, $fillcolor = false)
    {
        $im = $this->image;

        if (!$radius) {
            ImageFilledRectangle($im, $x1, $y1, $x2, $y2, $fillcolor);
            ImageRectangle($im, $x1, $y1, $x2, $y2, $linecolor);
            return;
        }

        if ($fillcolor) {
            imagefilledarc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $fillcolor, IMG_ARC_PIE);
            imagefilledrectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $fillcolor);
            imagefilledrectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $fillcolor);
        }
        imagearc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $linecolor);
        imagearc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $linecolor);
        imagearc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $linecolor);
        imagearc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $linecolor);
        imageline($im, $x1+$radius, $y1, $x2-$radius, $y1, $linecolor);
        imageline($im, $x1+$radius, $y2, $x2-$radius, $y2, $linecolor);
        imageline($im, $x1, $y1+$radius, $x1, $y2-$radius, $linecolor);
        imageline($im, $x2, $y1+$radius, $x2, $y2-$radius, $linecolor);
    }

}
