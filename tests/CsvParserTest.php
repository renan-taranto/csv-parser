<?php

declare(strict_types=1);

namespace Tests\Taranto\CsvParser;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Taranto\CsvParser\CsvParser;

class CsvParserTest extends TestCase
{
    /**
     * @var string Virtual CSV file 
     */
    private $csvFileName;
    
    public function setUp()
    {
        vfsStream::setup();
        vfsStream::newFile('file.csv');
        $this->csvFileName = vfsStream::url('root/file.csv');
        
        $fp = fopen($this->csvFileName, 'w');
        $list = [
            ['Brand', 'Modality', 'Color', chr(32)],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }
    
    public function testShouldThrowInvalidArgumentExceptionWhenFileDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CsvParser('non_existing_file.csv');
    }
    
    public function testShouldReturnCsvAsArray()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray  = [
            ['Brand', 'Modality', 'Color', chr(32)],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray());
    }
    
    public function testShouldReturnCsvAsArrayWithOffset()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray  = [
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride'],
            ['Landyachtz', "Downhill", '"Blue"', 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray(1));
    }

    public function testShouldReturnCsvAsArrayWithLimit()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray  = [
            ['Brand', 'Modality', 'Color', chr(32)],
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray(0, 3));
    }
  
    public function testShouldReturnCsvAsArrayWithOffsetAndLimit()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray  = [
            ['Sector 9', 'Freeride', 'White'],
            ['ABEC 11', 'Freeride']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsArray(1, 2));
    }

    public function testShouldReturnCsvAsAssociativeArrayIgnoringBlankHeaders()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray());
    }

    public function testShouldReturnCsvAsAssociativeArrayWithBlankHeaders()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White', chr(32) => ''],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => '', chr(32) => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"', chr(32) => 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray(0, 0, false));
    }
    
    public function testShouldReturnCsvAsAssociativeArrayWithOffset()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray(1));
    }
    
    public function testShouldReturnCsvAsAssociativeArrayWithLimit()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray(0, 1));
    }
    
    public function testShouldReturnCsvAsAssociativeArrayWithOffsetAndLimit()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => '']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray(1, 1));
    }
    
    public function testShouldReturnCsvAsAssociativeArrayWithOffsetAndLimitAndBlankHeaders()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $expectedArray = [
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"', chr(32) => 'Great downhill wheels']
        ];
        $this->assertEquals($expectedArray, $csvParser->getCsvAsAssociativeArray(2, 1, false));
    }
    
    public function testCanIterateTroughCsvAsArray()
    {
        $csvParser = new CsvParser($this->csvFileName);
        $returnedRows = [];
        $i = 0;
        foreach ($csvParser as $row) {
            $returnedRows[] = $row;
            $i++;
            if ($i === 2) {
                break;
            }
        }
        $expectedRows = [
            ['Brand', 'Modality', 'Color', chr(32)],
            ['Sector 9', 'Freeride', 'White']
        ];
        $this->assertEquals($expectedRows, $returnedRows);
    }
    
    public function testCanIterateTroughCsvAsAssociativeArray()
    {
        $csvParser = new CsvParser($this->csvFileName, true);
        $returnedRows = [];
        foreach ($csvParser as $row) {
            $returnedRows[] = $row;
        }
        $expectedRows = [
            ['Brand' => 'Sector 9', 'Modality' => 'Freeride', 'Color' => 'White'],
            ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''],
            ['Brand' => 'Landyachtz', 'Modality' => 'Downhill', 'Color' => '"Blue"']
        ];
        $this->assertEquals($expectedRows, $returnedRows);
    }
    
    public function testCanIterateTroughCsvAsAssociativeArrayWithOffsetAndLimit()
    {
        $csvParser = new CsvParser($this->csvFileName, true, true, 1, 1);
        $returnedRow = null;
        foreach ($csvParser as $row) {
            $returnedRow = $row;
        }
        $expectedRow = ['Brand' => 'ABEC 11', 'Modality' => 'Freeride', 'Color' => ''];
        $this->assertEquals($expectedRow, $returnedRow);
    }
}
