<?php

namespace app\models\reports;

use app\models\Model;

class PatientsReportModel extends Model
{

	protected string $table = 'mis_visits';

	public function __construct()
	{
		parent::__construct();
	}

	public function getFirstPaidVisits(string $dateFrom, string $dateTo, int $page, int $itemsOnPage): array|false|null
	{

		if (empty($dateFrom) || empty($dateTo)) {
			return false;
		}

		$pagination = '';
		if (!empty($page)) {
			$offset = ($page - 1) * $itemsOnPage;
			$pagination = "LIMIT {$itemsOnPage} OFFSET {$offset}";
		}

		$dbRequest = $this->_connection->query("SELECT * FROM (SELECT mv.patient_id, min(mv.vdate) as first_paid_visit FROM mis_visits mv WHERE mv.visit_summa > 0 AND mv.vdate BETWEEN '{$dateFrom}' AND '{$dateTo}' GROUP BY mv.patient_id order by first_paid_visit DESC) AS T " . $pagination);

		return $this->getCollection($dbRequest);

	}

	public function getCountOfItems(string $dateFrom, string $dateTo): int {

		$dbRequest = $this->_connection->query("SELECT COUNT(*) FROM (SELECT mv.patient_id FROM mis_visits mv WHERE mv.visit_summa > 0 AND mv.vdate BETWEEN '{$dateFrom}' AND '{$dateTo}' GROUP BY mv.patient_id) AS T");
		$count = $dbRequest->fetch_assoc();

		return array_shift($count);

	}

}