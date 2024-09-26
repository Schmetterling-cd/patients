<?php
return [
	'/api/reports/patients/firstPaidVisit' => ['controller' => 'reports/PatientsReportController', 'method' => 'getReport'],
	'/api/reports/patients/firstPaidVisit/download' => ['controller' => 'reports/PatientsReportController', 'method' => 'downloadReport'],
	'/api/reports/patients/firstPaidVisit/sendMail' => ['controller' => 'reports/PatientsReportController', 'method' => 'sendMail'],
];