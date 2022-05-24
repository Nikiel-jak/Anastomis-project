<?php

class Orion_ResizePhotoException extends Exception {
    
}

class Orion_ResizePhoto {

    private $img;
    private $bw = false;
    
    function __construct($imgfile) {
//	  global $notice;
        //detect image format
        //$this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
        //$this->img["format"]=strtoupper($this->img["format"]);
        $this->img["format"] = exif_imagetype($imgfile);
        if ($this->img["format"] == IMAGETYPE_JPEG) {
            //JPEG
            $this->img["src"] = ImageCreateFromJPEG($imgfile);
        } elseif ($this->img["format"] == IMAGETYPE_PNG) {
            //PNG
            $this->img["src"] = ImageCreateFromPNG($imgfile);
        } elseif ($this->img["format"] == IMAGETYPE_GIF) {
            //GIF
            $this->img["src"] = ImageCreateFromGIF($imgfile);
        } elseif ($this->img["format"] == IMAGETYPE_WBMP) {
            //WBMP
            $this->img["src"] = ImageCreateFromWBMP($imgfile);
        } else {
            //DEFAULT
            throw new Orion_ResizePhotoException('Zły format pliku.');
        }
        @$this->img["lebar"] = imagesx($this->img["src"]);
        @$this->img["tinggi"] = imagesy($this->img["src"]);
        //default quality jpeg
        $this->img["x"] = 0;
        $this->img["y"] = 0;
        $this->img["quality"] = 100;
        if (!$this->img["src"])
            throw new Orion_ResizePhotoException('Brak pliku do konwersji.');
    }

    function reset() {
        $this->img["x"] = 0;
        $this->img["y"] = 0;
    }

    function size_height($size = 100) {
        //height
        $this->img["tinggi_thumb"] = $size;
        @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"] / $this->img["tinggi"]) * $this->img["lebar"];
    }

    function size_width($size = 100) {
        //width
        $this->img["lebar_thumb"] = $size;
        @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"] / $this->img["lebar"]) * $this->img["tinggi"];
    }

    function size_auto($size = 100) {
        //size
        if ($this->img["lebar"] >= $this->img["tinggi"]) {
            $this->img["lebar_thumb"] = $size;
            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"] / $this->img["lebar"]) * $this->img["tinggi"];
        } else {
            $this->img["tinggi_thumb"] = $size;
            @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"] / $this->img["tinggi"]) * $this->img["lebar"];
        }
    }

    function autoSize($width, $height) {
        if ($this->img["lebar"] > $width || $this->img["tinggi"] > $height) {
            $this->img["lebar_thumb"] = $width;
            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"] / $this->img["lebar"]) * $this->img["tinggi"];
            if ($this->img["tinggi_thumb"] > $height) {
                $this->img["tinggi_thumb"] = $height;
                @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"] / $this->img["tinggi"]) * $this->img["lebar"];
            }
        } else {
            $this->img["lebar_thumb"] = $this->img["lebar"];
            $this->img["tinggi_thumb"] = $this->img["tinggi"];
        }
    }

    function jpeg_quality($quality = 100) {
        //jpeg quality
        $this->img["quality"] = $quality;
    }

    function cut($w, $h) {
        $this->size_width($w);
        if ($this->img["tinggi_thumb"] >= $h) {
            //echo 'Obraz jest wystarczajaco wysoki: '. $this->img["tinggi_thumb"] . '<br/>';
            $this->img["y"] = ( $this->img["tinggi_thumb"] - $h ) / 2 / $this->img["tinggi_thumb"] * $this->img["tinggi"];
            //echo 'Dlatego zaczne wycina� od x = ' . $this->img["x"];
            $this->img["tinggi_thumb"] = $h;
            return;
        }

        $this->size_height($h);
        if ($this->img["lebar_thumb"] >= $w) {
            $this->img["y"] = 0;
            $this->img["x"] = ( $this->img["lebar_thumb"] - $w ) / 2 / $this->img["lebar_thumb"] * $this->img["lebar"];
            $this->img["lebar_thumb"] = $w;
            return;
        }
    }

    function copy($x, $y, $w, $h, $tw, $th) {
        $this->img["x"] = $x;
        $this->img["y"] = $y;
        $this->img["w"] = $w;
        $this->img["h"] = $h;
        $this->img["lebar_thumb"] = $tw;
        $this->img["tinggi_thumb"] = $th;
    }
    
    function save($save = "") {
        //save thumb
        if (empty($save))
            $save = strtolower("./thumb." . $this->img["format"]);
        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function */
        //echo 'Zaczynam obcinac w ' . $this->img["x"] . ', ' . $this->img["y"] . '<br/>';
        //echo 'Obraz o wymiarach ' . ($this->img["lebar"] - (2 * $this->img["x"])) . ', ' . ($this->img["tinggi"] - (2 * $this->img["y"]));

        if($this->bw){
            imagefilter($this->img["src"], IMG_FILTER_GRAYSCALE);
        }

        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        if (isset($this->img["x"]) && isset($this->img["y"]) && isset($this->img["w"]) && isset($this->img["h"])) {
            @imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, $this->img["x"], $this->img["y"], $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["w"], $this->img["h"]);
        } else {
            @imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, $this->img["x"], $this->img["y"], $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"] - (2 * $this->img["x"]), $this->img["tinggi"] - (2 * $this->img["y"]));
        }
        if ($this->img["format"] == IMAGETYPE_JPEG) {
            //JPEG
            imageJPEG($this->img["des"], "$save", $this->img["quality"]);
            return true;
        } elseif ($this->img["format"] == IMAGETYPE_PNG) {
            //PNG
            imagePNG($this->img["des"], "$save");
            return true;
        } elseif ($this->img["format"] == IMAGETYPE_GIF) {
            //GIF
            imageGIF($this->img["des"], "$save");
            return true;
        } elseif ($this->img["format"] == IMAGETYPE_WBMP) {
            //WBMP
            imageWBMP($this->img["des"], "$save");
            return true;
        } else {
            return false;
        }
    }
    
    public function setBalckAndWhite($bw)
    {
        $this->bw = $bw;
    }

}
?>