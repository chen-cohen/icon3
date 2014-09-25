<?php


namespace LPO\Digest\Validator;

use finfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class File {

	/**
	 * @param string $dirPath
	 * @param array $extensionWhiteList
	 * @param array $MIMEWhiteList
	 * @return bool
	 */
	public function validateDir($dirPath, array $extensionWhiteList/*, array $MIMEWhiteList*/)
	{
		$extensionWhiteList = array_flip($extensionWhiteList);
//		$MIMEWhiteList 		= array_flip($MIMEWhiteList);

		$dirIterator = new RecursiveDirectoryIterator($dirPath);
		$iterator = new RecursiveIteratorIterator($dirIterator);

//		$fileValidator = new finfo(FILEINFO_MIME_TYPE);

		/**
		 * @var SplFileInfo $file
		 */
		foreach ($iterator as $file)
		{
//			$fileMIMEType = $fileValidator->file($file->getPathname());
			$filename = $file->getFilename();
			if($filename == '..' || $filename == '.') {continue;}
			if(/*!isset($MIMEWhiteList[$fileMIMEType]) || */!isset($extensionWhiteList[$file->getExtension()]))
			{
				return false;
				break;
			}
		}
		return true;
	}

	/**
	 * @param string $filePath
	 * @param array $extensionWhiteList
	 * @param array $MIMEWhiteList
	 * @return bool
	 */
	public function validateFile($filePath, array $extensionWhiteList/*, array $MIMEWhiteList*/)
	{
		$extensionWhiteList = array_flip($extensionWhiteList);
//		$MIMEWhiteList 		= array_flip($MIMEWhiteList);

		$extension = explode('.',$filePath);
//		$fileValidator = new finfo(FILEINFO_MIME_TYPE);
		return /*!isset($MIMEWhiteList[$fileValidator->file($filePath)]) || */!isset($extensionWhiteList[end($extension)]);
	}
}