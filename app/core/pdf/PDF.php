<?php

namespace app\core\pdf;

require_once 'lib/fpdf/fpdf.php';

class PDF extends \FPDF
{

	public function __construct($orientation, $unit, $size) {

		parent::__construct($orientation, $unit, $size);

	}

}