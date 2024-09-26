<?php

namespace app\services\storage\pdf;

use app\services\storage\FileStorageService;

class PdfFileStorageService extends FileStorageService
{

	protected const PDF_PREFIX = 'pdf';

	public function __construct()
	{

		parent::__construct();

		$this->_filesPath .= static::PDF_PREFIX . '/';
		$this->_filesExtension = static::PDF_PREFIX;

	}

}