<?php

namespace Tests\Taranto\CsvParser;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Taranto\CsvParser\CsvParser;

class CsvParserTest extends TestCase
{
    /**
     * @var string Path of the CSV fixture
     */
    private $csvFileName;
    
    /**
     * set up test environment 
     */
    public function setUp()
    {
        vfsStream::setup();
        vfsStream::newFile('file.csv');
        $this->csvFileName = vfsStream::url('root/file.csv');
        
        $fp = fopen($this->csvFileName, 'w');
        $list = [
            ['Brand', 'Modality', 'Color', ''],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        foreach ($list as $fields) {
            fputcsv($fp, $fields, ',');
        }
        fclose($fp);
    }
    
    public function testGetCsvAsArray()
    {
        $csvParser = new CsvParser();
        $expectedArray  = [
            ['Brand', 'Modality', 'Color', ''],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray($this->csvFileName));
    }
    
    public function testGetCsvAsArrayWithLimit()
    {
        $csvParser = new CsvParser();
        $expectedArray  = [
            ['Brand', 'Modality', 'Color', ''],
            ['Sector 9', 'Freeride', 'White'],
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray($this->csvFileName, ',', 2));
    }
    
    public function testGetCsvAsAssociativeArrayIgnoringBlankHeaders()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"'],
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName));
    }
    
    public function testGetCsvAsAssociativeArrayWithBlankHeaders()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White', '' => ''],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => '', '' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"', '' => 'Great downhill wheels'],
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', false));
    }
}
