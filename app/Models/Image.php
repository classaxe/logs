<?php

namespace App\Models;

class Image
{
    private $colors;
    private $fontDir;
    private $image;
    private $imageWidth;
    private $imageHeight;
    private $images;
    private $imageDir;

    public function __construct()
    {
        $this->imageDir = public_path('images') . '/';
        $this->fontDir =  resource_path('fonts') . '/';
    }

    private function getPathForFont($font)
    {
        $path = $this->fontDir . $font;
        if (file_exists($path)) {
            return $path;
        }
        dd('Path not found - ' .$path);
    }

    public function ImageColorAllocate($rgb)
    {
        $r =    hexdec(substr($rgb, 1,2));
        $g =    hexdec(substr($rgb, 3,2));
        $b =    hexdec(substr($rgb, 5,2));
        return ImageColorAllocate($this->image, $r, $g, $b);
    }

    public function getTextSize($points, $angle, $font, $text)
    {
        try {
            $rect = imagettfbbox($points, $angle, $this->getPathForFont($font), $text);

            $minX = min([$rect[0], $rect[2], $rect[4], $rect[6]]);
            $maxX = max([$rect[0], $rect[2], $rect[4], $rect[6]]);
            $minY = min([$rect[1], $rect[3], $rect[5], $rect[7]]);
            $maxY = max([$rect[1], $rect[3], $rect[5], $rect[7]]);
            return [
                "left"   => abs($minX) - 1,
                "top"    => abs($minY) - 1,
                "width"  => $maxX - $minX,
                "height" => $maxY - $minY,
                "box"    => $rect
            ];
        } catch(\Exception $e) {
            dd($e->getMessage() . " - " . $this->getPathForFont($font));
        }
    }

    public function ImageMake($width, $height) {
        $this->imageWidth = $width;
        $this->imageHeight = $height;
        $this->image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
        $this->ImageColorAllocateAll();
        imageFill($this->image, 0, 0, $this->colors['bg']);
    }

    public function ImageColorAllocateAll()
    {
        $this->colors = [
            'bg' =>         $this->ImageColorAllocate('#ffffff'),
            'black' =>      $this->ImageColorAllocate('#000000'),
            'white' =>      $this->ImageColorAllocate('#ffffff'),
            'darkgray' =>   $this->ImageColorAllocate('#808080'),
            'blue' =>       $this->ImageColorAllocate('#0000ff')
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

    public function ImageDrawText($points, $angle, $x, $y, $box_width, $box_height, $color, $font, $text)
    {
        imagettftext(
            $this->image,
            $points,
            $angle,
            $x + ($this->imageWidth / 2)  - ($box_width / 2),
            $y + ($this->imageHeight / 2) - ($box_height / 2),
            $this->colors[$color],
            $this->getPathForFont($font),
            $text
        );
    }

    public function ImageRender($type) {
        switch ($type) {
            case "gif":
                header("Content-Type: image/gif");
                imagegif($this->image);
                break;
            case "jpg":
                header("Content-Type: image/jpeg");
                imagejpeg($this->image);
                break;
            case "png":
                header("Content-Type: image/png");
                imagepng($this->image);
                break;
        }
        imagedestroy($this->image);
        die();
    }
}
