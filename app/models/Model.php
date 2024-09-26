<?php

namespace app\models;

use app\core\helpers\CollectionsHelper;
use mysqli;
use mysqli_result;

abstract class Model
{

	protected string $table = '';
	protected mysqli $_connection;

	public function __construct()
	{

		$this->_connection = new mysqli(
			env('DB_HOST', 'database'),
			env('DB_USER', 'patients'),
			env('DB_PASSWORD', 'er54z4q9'),
			env('DB_NAME', 'patients'),
			env('DB_PORT', 3306),
		);

	}

	public function __destruct()
	{

		$this->_connection->close();

	}

	protected function getCollection(mysqli_result $result) {

		$collection = $result->fetch_all();

		if (empty($collection)) {
			return [];
		}

		$fields = CollectionsHelper::objectCollectionToArrayCollection($result->fetch_fields());

		return CollectionsHelper::setCollectionItemsNames($collection, $fields);

	}

}