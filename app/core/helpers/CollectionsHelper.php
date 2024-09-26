<?php

namespace app\core\helpers;

final class CollectionsHelper
{

	public static function objectCollectionToArrayCollection(array $collectionOfObjects = []): array
	{

		if (empty($collectionOfObjects)) {
			return [];
		}

		$collectionOfArrays = [];
		foreach ($collectionOfObjects as $key => $field) {
			$collectionOfArrays[$key] = (array) $field;
		}

		return $collectionOfArrays;

	}

	public static function setCollectionItemsNames(array $collectionWithoutFieldsNames, array $fields): array
	{

		if (empty($collectionWithoutFieldsNames) || empty($fields)) {
			return $collectionWithoutFieldsNames;
		}

		$collection = [];
		foreach ($collectionWithoutFieldsNames as $key => $item) {
			foreach ($item as $fieldKey => $value) {
				$collection[$key][$fields[$fieldKey]['name']] = $value;
			}
		}

		return $collection;

	}

	public static function formatCollectionDateFields(array $collection, array $fields, string $format): array
	{

		foreach ($collection as &$item) {
			foreach ($fields as $field) {
				$item[$field] = ServiceFormatHelper::formatDate($item[$field], $format);
			}
		}

		return $collection;

	}

	public static function getCamelCaseKeysForCollection(array $collection): array
	{

		if (empty($collection)) {
			return [];
		}

		foreach ($collection as &$item) {
			foreach ($item as $key => $value) {
				$item[ServiceFormatHelper::getCamelCase($key, '_')] = $value;
				unset($item[$key]);
			}
		}

		return $collection;

	}

}