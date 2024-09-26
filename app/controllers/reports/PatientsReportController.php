<?php

namespace app\controllers\reports;

use app\controllers\Controller;
use app\services\reports\PatientsReportService;

class PatientsReportController extends Controller
{

	public function getReport(): void
	{

		$service = new PatientsReportService($_GET);

		if (!$service->isValid()) {
			exit($service->getApiError()) ;
		}

		$report = $service->getReport();
		if ($report === false) {
			exit($service->getApiError());
		}

		exit($service->getApiResponse($report));

	}

	public function downloadReport(): void
	{

		$service = new PatientsReportService($_GET);

		$service->setValidationFilters([
			'file' => true,
		]);

		if (!$service->isValid()) {
			exit($service->getApiError()) ;
		}

		$report = $service->downloadReport();
		if ($report === false) {
			exit($service->getApiError());
		}

		exit();

	}

	public function sendMail()
	{

		$service = new PatientsReportService($_GET);

		$service->setValidationFilters([
			'mail' => true,
		]);

		if (!$service->isValid()) {
			exit($service->getApiError()) ;
		}

		if($service->sendReport()) {
			exit($service->getApiInfo());
		}

		exit($service->getApiError());

	}

}