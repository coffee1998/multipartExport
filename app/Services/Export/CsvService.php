<?php

namespace App\Services\Export;

use ExcePtion;

class CsvService
{
    /**
     * file name
     * @var string
     */
    protected $fileName = '';

    /**
     * file path
     * @var string
     */
    public $filePath = null;

    /**
     * fopen file
     * @var pointer|null
     */
    protected $pointFile = null;

    /**
     * set export file name
     * @param string $fileName
     * @author nicoke
     * @date 2018-02-01
     */
    public function setFileName($fileName = '')
    {
        if (!$fileName) {
            $fileName = 'export_csv_' . uniqid();
        }

        $this->fileName = $fileName;

        $this->setFilePath();
    }

    /**
     * get the file name
     * @author nicoke
     * @date 2018-02-01
     * @return  string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * set the file path. default: storage folder
     * @author nicoke
     * @date 2018-02-01
     * @return   string
     */
    protected function setFilePath()
    {
        $path = storage_path() . '/exports/' . \Carbon\Carbon::now()->format('Ymd') . '/';

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->filePath = $path . $this->fileName . '.csv';
    }

    /**
     * get the file path
     * @author nicoke
     * @date 2018-02-01
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Determine if the file exists
     * @param string $filePath
     * @author nicoke
     * @date 2018-02-01
     * @return boolean
     */
    public function fileExist($filePath = '')
    {
        if (!$filePath) {
            $filePath = $this->filePath;
        }

        return file_exists($this->filePath);
    }

    /**
     * download file
     * @param string $filePath
     * @author nicoke
     * @date 2018-02-01
     * @return \Illuminate\Http\Response
     */
    public function download($filePath = '')
    {
        if (!$filePath) {
            $filePath = $this->filePath;
        }

        return response()->download($filePath);
    }

    /**
     * unlink file
     * @author nicoke
     * @date 2018-02-01
     * @return   boolean
     */
    public function unLinkFile()
    {
        if (!$this->fileExist()) {
            return false;
        }

        return unlink($this->filePath);
    }

    /**
     * write a row of data
     * @param array
     * @author nicoke
     * @date 2018-02-01
     */
    public function addRow(array $data)
    {
        if (!$this->pointFile) {
            if (!$this->injectFile()) {
                throw new Exception("打开文件失败");
            }
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->charSetToGBK($value);
        }

        // TODO: 添加写入到文件 还是直接输出到浏览器
        //$this->write($data);
        fputcsv($this->pointFile, $data);
    }

    /**
     * Returns a file pointer resource on success, or FALSE on error.
     * @author nicoke
     * @date 2018-02-01
     * @return pointer|boolean
     */
    public function injectFile()
    {
        if (!is_null($this->pointFile)) {
            return $this->pointFile;
        }

        $this->pointFile = fopen($this->filePath, 'a+');

        return $this->pointFile;
    }

    /**
     * charset GBK
     * @param string $value
     * @author nicoke
     * @date 2018-02-01
     * @return string
     */
    public function charSetToGBK($value)
    {
         return mb_convert_encoding($value,'gbk', 'utf-8');
    }

    public function finish()
    {

    }

    /**
     *TODO:添加写入到文件 还是直接输出到浏览器
     */
    protected function write($data)
    {
		switch($this->exportTo) {
			case 'browser':
				echo $data;
				break;
			case 'string':
				$this->stringData .= $data;
				break;
			case 'file':
				fwrite($this->pointFile, $data);
				break;
		}
	}

    public function getExportProcess()
    {

    }

    public function getCompletedProgress()
    {

    }
}
