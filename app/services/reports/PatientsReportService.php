<?php

namespace app\services\reports;

use app\core\helpers\CollectionsHelper;
use app\core\helpers\PageViewHelper;
use app\core\pdf\PDF;
use app\models\reports\PatientsReportModel;
use app\services\mail\MailService;
use app\services\Service;
use app\services\storage\pdf\PdfFileStorageService;

class PatientsReportService extends Service
{

	protected const FORMAT_EMAIL = '/^.+@.+\..+$/';

	private const ITEMS_ON_PAGE = 10;

	protected const REPORT_FORMATS = ['PDF'];
	protected const REPORT_PDF_ORIENTATION = 'P';
	protected const REPORT_PDF_UNIT = 'mm';
	protected const REPORT_PDF_FORMAT = 'A4';
	protected const REPORT_PDF_MARGIN_LEFT = 25;
	protected const REPORT_PDF_TEXT_PATIENT = '%number. Пациент: %id, дата первого оплаченного посещения: %date';


	public function __construct(array $_data)
	{
		parent::__construct($_data);
	}

	public function isValid(): bool
	{

		$data = $this->getData();

		if (empty($data['dateFrom'])) {
			$this->setError('Дата начала периода обязательна.');
			return false;
		}

		if (empty($data['dateTo'])) {
			$this->setError('Дата конца периода обязательна.');
			return false;
		}

		if (date_create($data['dateFrom']) > date_create($data['dateTo'])) {
			$this->setError('Дата начала не может быть позже даты конца.');
			return false;
		}

		if (!empty($this->_validationFilters['file'])) {
			if (empty($data['fileExt'] || !in_array($data['fileExt'], static::REPORT_FORMATS))) {
				$this->setError('Данный тип отчёта не поддерживается.');
				return false;
			}
		}

		if (!empty($this->_validationFilters['mail'])) {
			if (empty($data['mail'])) {
				$this->setError('Необходимо указать почту для направления отчёта.');
				return false;
			}

			if (!preg_match(static::FORMAT_EMAIL, $data['mail'])) {
				$this->setError('Формат вводимой почты должен соответствовать "example@type.domen".');
				return false;
			}
		}

		return true;

	}

	public function getReport(): false|array
	{

		$model = $this->getModel(new PatientsReportModel());
		$visits = $model->getFirstPaidVisits($this->_data['dateFrom'], $this->_data['dateTo'], $this->_data['page'] ?? 0, static::ITEMS_ON_PAGE);

		if ($visits === false) {
			$this->setError('Произошла ошибка при формировании запроса.');
			return false;
		}

		$visits = CollectionsHelper::getCamelCaseKeysForCollection($visits);
		$countOfItems = $model->getCountOfItems($this->_data['dateFrom'], $this->_data['dateTo']);

		return [
			'firstPaidVisits' => CollectionsHelper::formatCollectionDateFields($visits, ['firstPaidVisit'], 'd.m.Y'),
			'countOfPages' => PageViewHelper::countPages($countOfItems, static::ITEMS_ON_PAGE),
		];

	}

	public function downloadReport()
	{

		$report = $this->getReport();
		if ($report === false) {
			return false;
		}

		switch ($this->_data['fileExt']) {
			case 'PDF':
				$pdf = $this->createPdfReport($report['firstPaidVisits']);
				$fileName = date_format(date_create($this->_data['dateFrom']), 'd_m_Y') . '-' . date_format(date_create($this->_data['dateTo']), 'd_m_Y') . ' первое посещений пациентов с оплатой.pdf';
				return $pdf->Output('D', $fileName, true);
		}

		$this->setError('Произошла ошибка при формировании отчёта.');
		return false;

	}

	public function createPdfReport(array $report)
	{

		$pdf = new PDF(static::REPORT_PDF_ORIENTATION, static::REPORT_PDF_UNIT, static::REPORT_PDF_FORMAT);

		$pdf->SetFillColor(242, 219, 219);
		$pdf->AddPage();
		$pdf->AddFont('TimesNewRomanPSMT', '', 'times.php');
		$pdf->AddFont('TimesNewRomanPS-BoldMT', '', 'times_new_bold.php');

		$pdf->SetLeftMargin(static::REPORT_PDF_MARGIN_LEFT);

		$pdf->SetFont('TimesNewRomanPS-BoldMT', '', 14);

		$pdf->Cell(0, 7,  mb_convert_encoding('Технический список посещений пациентов', 'windows-1251', 'utf-8') , 0, 0, 'C');
		$pdf->Ln();

		$pdf->Cell(0, 7,  mb_convert_encoding('(впервые с оплатой)', 'windows-1251', 'utf-8') , 0, 0, 'C');
		$pdf->Ln(10);

		$pdf->SetFont('TimesNewRomanPSMT', '', 14);

		foreach ($report as $number => $reportLine) {
			$lastChar = array_key_last($report) === $number ? '.' : ';';
			$lineText = str_replace(['%number', '%id', '%date'], [$number, $reportLine['patientId'], $reportLine['firstPaidVisit']],static::REPORT_PDF_TEXT_PATIENT);
			$pdf->Cell(1, 6, mb_convert_encoding($lineText . $lastChar, 'windows-1251', 'utf-8'));
			$pdf->Ln();
		}

		return $pdf;

	}

	public function saveFileInStorage() {

	}

	public function sendReport()
	{

		$report = $this->getReport();
		$pdf = $this->createPdfReport($report['firstPaidVisits']);
		$pdf = $pdf->Output('S');

		$fileService = new PdfFileStorageService();
		$file = $fileService->createFile();
		$filePath = $fileService->getFullPathToFile($file);
		$fileService->putContentToFile($filePath, $pdf);

		$attachments = [
			[
				'path' => $filePath,
				'name' => $file,
			]
		];

		$mailService = new MailService();

		return $mailService->sendMail($this->_data['mail'], 'Отчёт', 'Данные о первых оплачкенных посещениях.', $attachments);

	}

}