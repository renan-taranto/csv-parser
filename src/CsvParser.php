<?php

namespace Taranto\CsvParser;

/**
 * CSV Parser
 *
 * @author Renan Taranto <renantaranto@gmail.com>
 */
class CsvParser
{
    public function getCsvAsAssociativeArray(
        string $fileName,
        string $delimiter = ',',
        bool $ignoreBlankHeaders = true,
        int $offset = 0,
        int $limit = 0
    ): array
    {
        $header = $this->createHeader($this->getCsvAsArray($fileName, $delimiter, 0, 1), $ignoreBlankHeaders);
        $csvAsArray = $this->getCsvAsArray($fileName, $delimiter, $offset + 1, $limit);
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

    public function getCsvAsArray(string $fileName, string $delimiter = ',', int $offset = 0, int $limit = 0): array
    {
        $fp = fopen($fileName, "r");
        $csvAsArray = [];
        
        $currentRowIndex = 0;
        $numberOfRowsParsed = 0;
        while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {
            if ($offset !== 0 and $currentRowIndex < $offset) {
                $currentRowIndex++;
                continue;
            }
            
            if ($limit !== 0 and $numberOfRowsParsed >= $limit) {
                return $csvAsArray;
            }
            
            $csvAsArray[] = $row;
            $currentRowIndex++;
            $numberOfRowsParsed++;
        }
        
        fclose($fp);
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
