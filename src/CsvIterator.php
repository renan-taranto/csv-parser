<?php

declare(strict_types=1);

namespace Taranto\CsvParser;

/**
 * @author Renan Taranto <renantaranto@gmail.com>
 */
class CsvIterator implements \Iterator
{
    /**
     * @var mixed
     */
    private $filePointer;
    
    /**
     * @var string
     */
    private $delimiter;
    
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
    
    /**
     * @param string $fileName
     * @param string $delimiter
     */
    public function __construct(string $fileName, string $delimiter)
    {
        $this->filePointer = fopen($fileName, "r");
        $this->delimiter = $delimiter;
    }

    /**
     * @param bool $ignoreBlankHeaders Whether first line blank cells should be 
     *                                used as header
     * @return CsvIterator
     */
    public function useFirstRowAsHeader(bool $ignoreBlankHeaders = true): self
    {
        if ($ignoreBlankHeaders) {
            $this->header = array_filter(
                fgetcsv($this->filePointer, 0, $this->delimiter),
                function($el) {
                    return !empty(trim($el));
                }
            );
            return $this;
        }
        $this->header = fgetcsv($this->filePointer, 0, $this->delimiter);
        return $this;
    }
    
    /**
     * @param int $limit
     * @return CsvIterator
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return CsvIterator
     */
    public function applyOffset(int $offset): self
    {
        for ($i = 0; $i < $offset; $i++) {
            fgetcsv($this->filePointer, 0, $this->delimiter);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function current(): array
    {
        $row = fgetcsv($this->filePointer, 0, $this->delimiter);
        if (!$this->header) {
            return $row;
        }
        
        $indexedRow = [];
        $numberOfHeaders = count($this->header);
        for ($i = 0; $i < $numberOfHeaders; $i++) {
            $indexedRow[$this->header[$i]] = isset($row[$i]) ? $row[$i] : '';
        }
        return $indexedRow;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->rowCounter;
    }

    public function next()
    {
        $this->rowCounter++;
    }

    public function rewind()
    {
        $this->rowCounter = 0;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return
            $this->filePointer &&
            !$this->isEndOfFile() &&
            !($this->limit && $this->rowCounter >= $this->limit)
        ;
    }
    
    /**
     * @return bool
     */
    private function isEndOfFile(): bool
    {
        $currentFpPosition = ftell($this->filePointer);
        
        if (!fgetc($this->filePointer)) {
            return true;
        }
        
        fseek($this->filePointer, $currentFpPosition);
        return false;
    }
    
    public function __destruct()
    {
        fclose($this->filePointer);
    }
}
