<?php

namespace Taranto\CsvParser;

/**
 * CSV Parser
 *
 * @author Renan Taranto <renantaranto@gmail.com>
 */
class CsvParser
{
    public function getCsvAsAssociativeArray(string $fileName, string $delimiter = ',', bool $ignoreBlankHeaders = true): array
    {
        $csvAsArray = $this->getCsvAsArray($fileName, $delimiter);
        $header = $this->createHeader($csvAsArray, $ignoreBlankHeaders);
        array_shift($csvAsArray);
        $csvAsAssociativeArray = [];
        foreach ($csvAsArray as $row) {
            $indexedRow = [];
            for ($i = 0; $i < count($header); $i++) {
                $indexedRow[$header[$i]] = isset($row[$i]) ? $row[$i] : '';
            }
            $csvAsAssociativeArray[] = $indexedRow;
        }
        return $csvAsAssociativeArray;
    }

    public function getCsvAsArray(string $fileName, string $delimiter = ',', int $limit = null): array
    {
        $fp = fopen($fileName, "r");
        $csvAsArray = [];
        
        $currentLineIndex = 0;
        while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {
            if ($limit and $currentLineIndex >= $limit) {
                return $csvAsArray;
            }
            $csvAsArray[] = $row;
            $currentLineIndex++;
        }
        return $csvAsArray;
    }
    
    private function createHeader(array $csvAsArray, bool $ignoreBlankHeaders): array
    {
        if ($ignoreBlankHeaders) {
            return array_filter(array_shift($csvAsArray));
        }
        
        return array_shift($csvAsArray);
    }

}
