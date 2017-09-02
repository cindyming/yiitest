<?php
namespace app\models;
use yii\base\Exception;
/**
 * Class for export array data to csv file
 *
 *
 * CSVExport::Export([
'dirName' => Yii::getAlias('@webroot'),
'fileName' => 'users.csv',
'data' => [
['#', 'User Name', 'Email'],
['1', 'Serhiy Novoseletskiy', 'novoseletskiyserhiy@gmail.com']
]
]);
 */
class CSVExport
{
	private static $data;
	private static $dirName;
	private static $fileName;
	/**
	 * @param array $options
	 * @return string
	 * @throws \yii\base\Exception
	 */
	public static function Export(array $options = [], $sheetName)
	{
		static::$data = isset($options['data']) ? $options['data'] : [];
		static::$fileName = isset($options['fileName']) ? $options['fileName'] : 'file.csv';
		if (!isset($options['dirName'])) {
			throw new Exception('You must set dirName');
		}
		static::$dirName = $options['dirName'];
		if (static::$dirName[strlen(static::$dirName - 1)] !== '/') {
			static::$dirName .= '/';
		}
		return self::array2csv(static::$data, static::$dirName, static::$fileName, $sheetName);
	}
	/**
	 * @param array $array
	 * @param $dirName
	 * @param $fileName
	 * @return string
	 */
	private static function array2csv(array &$array, $dirName, $fileName, $sheetName)
	{
		if (!is_dir($dirName)) {
			mkdir($dirName);
		}
		ob_start();

		$objExcel = new \PHPExcel();

		$objProps = $objExcel->getProperties();
		$objProps->setCreator($sheetName);
		$objProps->setTitle($sheetName);
		$objProps->setCategory($sheetName);

		$objExcel->setActiveSheetIndex(0);
		$objExcel->getActiveSheet()->setTitle($sheetName);


		$i = 1;
		foreach ($array as $row) {
			$j = 0;
			foreach ($row as $d) {
				$column = self::IntToChr($j);
				$objExcel->getActiveSheet()->setCellValue($column . ($i), $d);
				$j++;
			}
			$i++;

		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $sheetName . '.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
		$objWriter->save($dirName . $fileName);

		ob_get_clean();
		return $dirName . $fileName;
	}

	public static function convertUTF8($str)
	{
		if (empty($str)) {
			return '';
		} else {
			return  iconv('GB2312', 'utf-8', $str);
		}
	}

	public static function IntToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= self::IntToChr(floor($index / 26)-1);
        }
		return $str . chr($index % 26 + $start);
	}
}