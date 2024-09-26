<?php

namespace app\services;

use stdClass;

abstract class Service
{

	protected const STATUS_ERROR = 'ERROR';
	protected const STATUS_OK = 'OK';

	protected array $_data = [];
	protected string $_error = '';
	protected object $_models;
	protected array $_validationFilters;

	public function __construct(array $_data)
	{

		$this->_models = new stdClass();
		$this->setData($_data);

	}

	public function setData($data): void
	{

		$this->_data = $data;

	}

	public function setValidationFilters(array $filters = array()): void
	{

		$this->_validationFilters = $filters;

	}

	public function getData(): array
	{

		return $this->_data;

	}

	protected function getModel($model)
	{

		if (is_object($model)) {
			$modelNameWithPath = get_class($model);
			$nameArray = explode('\\', $modelNameWithPath);
			$modelName = end($nameArray);
		} else {
			$modelNameWithPath = 'App\\Models\\' . $model;
			$modelName = $model;
		}

		if (property_exists($this->_models, $modelName)) {
			return $this->_models->$modelName;
		}

		if (class_exists($modelNameWithPath)) {
			if (is_object($model)) {
				$this->_models->$modelName = $model;

				return $this->_models->$modelName;
			}

			$this->_models->$modelName = new $modelNameWithPath();

			return $this->_models->$modelName;
		}

		$this->setError('Запрашиваемая модель не существует.');

		return false;

	}

	protected function setError(string $error): void
	{

		$this->_error = $error;

	}

	public function getError(): string
	{

		return $this->_error;

	}

	public function getApiResponse($data = array()): false|string
	{

		if (empty($data)) {
			return ['status' => static::STATUS_OK];
		}

		return json_encode([
			'status' => static::STATUS_OK,
			'data' => $data,
		]);

	}

	public function getApiError(): false|string
	{

		$error = $this->getError();

		return json_encode([
			'status' => static::STATUS_ERROR,
			'message' => empty($error) ? 'Сервис временно недоступен.' : $error,
		]);

	}

	public function getApiInfo(): false|string {

		return json_encode([
			'status' => static::STATUS_ERROR,
			'message' => 'Выполнено успешно!',
		]);

	}

}