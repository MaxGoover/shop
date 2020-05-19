<?php

namespace shop\services;

use PHPThumb\GD;
use Yii;

class WaterMarker
{
    private $_width;
    private $_height;
    private $_watermark;

    public function __construct($width, $height, $watermark)
    {
        $this->_width = $width;
        $this->_height = $height;
        $this->_watermark = $watermark;
    }

    public function process(GD $thumb): void
    {
        $watermark = new GD(Yii::getAlias($this->_watermark));
        $source = $watermark->getOldImage();

        if (!empty($this->_width) || !empty($this->_height)) {
            $thumb->adaptiveResize($this->_width, $this->_height);
        }

        $originalSize = $thumb->getCurrentDimensions();
        $watermarkSize = $watermark->getCurrentDimensions();

        $destinationX = $originalSize['width'] - $watermarkSize['width'] - 10;
        $destinationY = $originalSize['height'] - $watermarkSize['height'] - 10;

        $destination = $thumb->getOldImage();

        \imagealphablending($source, true);
        \imagealphablending($destination, true);

        \imagecopy(
            $destination,
            $source,
            $destinationX, $destinationY,
            0, 0,
            $watermarkSize['width'], $watermarkSize['height']
        );

        $thumb->setOldImage($destination);
        $thumb->setWorkingImage($destination);
    }
} 