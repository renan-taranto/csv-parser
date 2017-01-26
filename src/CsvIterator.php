<?php

declare(strict_types=1);

namespace Taranto\CsvParser;

class CsvIterator implements \Iterator
{
    /**
     * @var mixed
     */
    private $filePointer;
    
    /**
     * @var int 
     */
    private $offset;
    
    /**
     * @var int
     */
    private $limit;
    
    /**
     * @var int
     */
    private $rowCounter = 0;
    
    /**
     * @var array
     */
    private $header;
    
    public function __construct(string $fileName)
    {
        $this->filePointer = fopen($fileName, "r");
    }

    /**
     * @param bool $ignoreBlankHeaders Whether first line blank cells should be 
     *                                used as header
     * @return void
     */
    public function useFirstRowAsHeader(bool $ignoreBlankHeaders = true): void
    {
        if ($ignoreBlankHeaders) {
            $this->header = array_filter(fgetcsv($this->filePointer, 0, ','));
            return;
        }
        $this->header = fgetcsv($this->filePointer, 0, ',');
    }
    
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function applyOffset(int $offset): void
    {
        $this->offset = $offset;
        for ($i = 0; $i < $offset; $i++) {
            fgetcsv($this->filePointer, 0, ',');
        }
    }
        
    public function current()
    {
        $row = fgetcsv($this->filePointer, 0, ',');
        if (!$this->header) {
            return $row;
        }
        
        $indexedRow = [];
        for ($i = 0; $i < count($this->header); $i++) {
            $indexedRow[$this->header[$i]] = isset($row[$i]) ? $row[$i] : '';
        }
        return $indexedRow;
    }

    public function key(): int
    {
        return $this->rowCounter;
    }

    public function next(): void
    {
        $this->rowCounter++;
    }

    public function rewind(): void
    {
        $this->rowCounter = 0;
    }

    public function valid(): bool
    {
        if (
            !$this->filePointer or
            feof($this->filePointer) or
            ($this->limit and $this->rowCounter >= $this->limit)
        ) {
            return false;
        }
        return true;
    }

}
