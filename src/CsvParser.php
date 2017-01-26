<?php

declare(strict_types=1);

namespace Taranto\CsvParser;

/**
 * CSV Parser
 *
 * @author Renan Taranto <renantaranto@gmail.com>
 */
class CsvParser implements \IteratorAggregate
{
    /**
     * @var CsvIterator;
     */
    private $iterator;
    
    public function __construct(
        string $fileName,
        bool $asAssociativeArray = false,
        bool $ignoreBlankHeaders = true,
        int $offset = 0,
        int $limit = 0,
        string $delimiter = null
    ) {
        $this->throwInvalidArgumenExceptionIfFileNotFound($fileName);
        
        $this->iterator = new CsvIterator($fileName);
        if ($asAssociativeArray) {
            $this->iterator->useFirstRowAsHeader($ignoreBlankHeaders);
        }
        $this->iterator->applyOffset($offset);
        $this->iterator->setLimit($limit);
    }
    
    
    private function throwInvalidArgumenExceptionIfFileNotFound(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException('File not found: ' . $fileName);
        }
    }
    
    public function getCsvAsArray(int $offset = 0, int $limit = 0): array
    {
        $this->iterator->applyOffset($offset);
        $this->iterator->setLimit($limit);
        
        return iterator_to_array($this->iterator);
    }
    
    public function getCsvAsAssociativeArray(int $offset = 0, int $limit = 0, bool $ignoreBlankHeaders = true): array
    {
        $this->iterator->useFirstRowAsHeader($ignoreBlankHeaders);
        $this->iterator->applyOffset($offset);
        $this->iterator->setLimit($limit);
        
        return iterator_to_array($this->iterator);
    }
    
    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }
}
