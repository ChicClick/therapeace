<?php
require_once __DIR__ . '/fpdf/fpdf.php';
require_once __DIR__ . '/fpdi/src/autoload.php';

class CustomFPDI extends \setasign\Fpdi\Fpdi {
    // Updated Ellipse function without BeginText/EndText
    function Ellipse($x, $y, $rx, $ry, $style='D') {
        if ($style == 'F') {
            $op = 'f';  // Filled ellipse
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';  // Filled and bordered ellipse
        } else {
            $op = 'S';  // Border only
        }
        
        $lx = 4/3 * (M_SQRT2 - 1) * $rx;
        $ly = 4/3 * (M_SQRT2 - 1) * $ry;
        
        $this->_out(sprintf('%.2F %.2F m', ($x+$rx)*$this->k, ($this->h-$y)*$this->k));
        $this->_Arc($x+$rx, $y-$ly, $x+$lx, $y-$ry, $x, $y-$ry);
        $this->_Arc($x-$lx, $y-$ry, $x-$rx, $y-$ly, $x-$rx, $y);
        $this->_Arc($x-$rx, $y+$ly, $x-$lx, $y+$ry, $x, $y+$ry);
        $this->_Arc($x+$lx, $y+$ry, $x+$rx, $y+$ly, $x+$rx, $y);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k,
            $x3 * $this->k, ($h - $y3) * $this->k));
    }
}
