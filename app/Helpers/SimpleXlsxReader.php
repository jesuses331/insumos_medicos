<?php

namespace App\Helpers;

use ZipArchive;
use SimpleXMLElement;

class SimpleXlsxReader
{
    protected $xml;
    protected $sharedStrings = [];
    protected $rows = [];

    public function __construct($filename)
    {
        $zip = new ZipArchive();
        if ($zip->open($filename) === true) {
            // Load shared strings
            if (($content = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
                $xml = new SimpleXMLElement($content);
                foreach ($xml->si as $si) {
                    $this->sharedStrings[] = (string) ($si->t ?? $si->r->t);
                }
            }

            // Load sheet1.xml
            if (($sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml')) !== false) {
                $xml = new SimpleXMLElement($sheetContent);
                foreach ($xml->sheetData->row as $row) {
                    $rowData = [];
                    foreach ($row->c as $c) {
                        $value = (string) $c->v;
                        if ((string) $c['t'] === 's') {
                            $value = $this->sharedStrings[(int) $value] ?? '';
                        }
                        $rowData[] = $value;
                    }
                    $this->rows[] = $rowData;
                }
            }
            $zip->close();
        }
    }

    public function getRows()
    {
        return $this->rows;
    }

    public static function parse($filename)
    {
        $reader = new self($filename);
        return $reader->getRows();
    }
}
