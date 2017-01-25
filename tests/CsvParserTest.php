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
    
    public function testGetCsvAsArrayWithOffset()
    {
        $csvParser = new CsvParser();
        $expectedArray  = [
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray($this->csvFileName, ',', 1));
    }
    
    public function testGetCsvAsArrayWithLimit()
    {
        $csvParser = new CsvParser();
        $expectedArray  = [
            ['Brand', 'Modality', 'Color', ''],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray($this->csvFileName, ',', 0, 3));
    }
    
    public function testGetCsvAsArrayWithOffsetAndLimit()
    {
        $csvParser = new CsvParser();
        $expectedArray  = [
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray($this->csvFileName, ',', 1, 2));
    }
    
    public function testGetCsvAsAssociativeArrayIgnoringBlankHeaders()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName));
    }
    
    public function testGetCsvAsAssociativeArrayWithBlankHeaders()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White', '' => ''],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => '', '' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"', '' => 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', false));
    }
    
    public function testGetCsvAsAssociativeArrayWithOffset()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', true, 1));
    }
    
    public function testGetCsvAsAssociativeArrayWithLimit()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', true, 0, 1));
    }
    
    public function testGetCsvAsAssociativeArrayWithOffsetAndLimit()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => '']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', true, 1, 1));
    }
    
    public function testGetCsvAsAssociativeArrayWithOffsetAndLimitAndBlankHeaders()
    {
        $csvParser = new CsvParser();
        $expectedArray = [
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"', '' => 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray($this->csvFileName, ',', false, 2, 1));
    }
}
