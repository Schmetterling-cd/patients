<?php

namespace app\services\storage;

class FileStorageService
{

	public const PATH_STORAGE_FILES = '/storage/files/';

	protected string $_filesPath;
	protected string $_filesExtension;

	public function __construct()
	{

		$this->_filesPath = $_SERVER['DOCUMENT_ROOT'] . static::PATH_STORAGE_FILES;
		$this->_filesExtension = 'txt';

		$this->crateDirectoryForFiles();

	}

	public function createFile(string $fileName = ''): string
	{

		if (empty($fileName)) {
			$fileName = time();
		}

		$file = fopen($this->_filesPath . $fileName . '.' . $this->_filesExtension, 'w+');
		fclose($file);

		return $fileName . '.' . $this->_filesExtension;

	}

	public function getFullPathToFile(string $file): string
	{

		return $this->_filesPath . $file;

	}

	public function putContentToFile(string $file, string $content): bool
	{

		return (bool) file_put_contents($file, $content);

	}

	protected function crateDirectoryForFiles(): bool
	{

		if (!is_dir($this->_filesPath)) {
			return mkdir($this->_filesPath);
		}

		return true;

	}

}