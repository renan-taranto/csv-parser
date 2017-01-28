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
    
    /**
     * @param string $fileName
     * @param bool $asAssociativeArray
     * @param bool $ignoreBlankHeaders
     * @param int $offset
     * @param int $limit
     * @param string $delimiter
     */
    public function __construct(
        string $fileName,
        bool $asAssociativeArray = false,
        bool $ignoreBlankHeaders = true,
        int $offset = 0,
        int $limit = 0,
        string $delimiter = null
    ) {
        $this->throwInvalidArgumenExceptionIfFileNotFound($fileName);
        
        if (!$delimiter) {
            $delimiter = $this->guessDelimiter($fileName);
        }
        
        $this->iterator = new CsvIterator($fileName, $delimiter);
        if ($asAssociativeArray) {
            $this->iterator->useFirstRowAsHeader($ignoreBlankHeaders);
        }
        $this->iterator->applyOffset($offset)
            ->setLimit($limit);
    }
    
    /**
     * @param string $fileName
     * @return string
     */
    private function guessDelimiter($fileName): string
    {
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        );

        $filePointer = fopen($fileName, "r");
        $firstLine = fgets($filePointer);
        fclose($filePointer); 
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
    
    /**
     * @param string $fileName
     * @throws \InvalidArgumentException
     */
    private function throwInvalidArgumenExceptionIfFileNotFound(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException('File not found: ' . $fileName);
        }
    }
    
    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getCsvAsArray(int $offset = 0, int $limit = 0): array
    {
        $this->iterator->applyOffset($offset)
            ->setLimit($limit);
        
        return iterator_to_array($this->iterator);
    }
    
    /**
     * @param int $offset
     * @param int $limit
     * @param bool $ignoreBlankHeaders
     * @return array
     */
    public function getCsvAsAssociativeArray(int $offset = 0, int $limit = 0, bool $ignoreBlankHeaders = true): array
    {
        $this->iterator->useFirstRowAsHeader($ignoreBlankHeaders)
            ->applyOffset($offset)
            ->setLimit($limit);
        
        return iterator_to_array($this->iterator);
    }
    
    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }
}
