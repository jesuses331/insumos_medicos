<?php

// Script to generate a minimal valid XLSX template for inventory import
// Using ZipArchive and internal XML structure

$filename = 'f:/Proyectos Laravel/pantallas/public/templates/inventario_plantilla.xlsx';

$zip = new ZipArchive();
if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    // [Content_Types].xml
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>');

    // _rels/.rels
    $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');

    // xl/workbook.xml
    $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Inventario" sheetId="1" r:id="rId1"/></sheets></workbook>');

    // xl/_rels/workbook.xml.rels
    $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>');

    // xl/sharedStrings.xml - Header and sample data
    $strings = ['marca', 'modelo', 'tipo', 'costo', 'precio_venta', 'categoria', 'stock', 'Samsung', 'A50', 'Incell', 'pantalla', 'iPhone', '11', 'Original', 'Huawei', 'P30 Lite', 'AAA'];
    $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
    foreach ($strings as $s) {
        $sharedStringsXml .= '<si><t>' . htmlspecialchars($s) . '</t></si>';
    }
    $sharedStringsXml .= '</sst>';
    $zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);

    // xl/worksheets/sheet1.xml - Row and Cell data
    // Row 1 (Header): 0-6
    // Row 2 (Samsung): 7, 8, 9, (float), (float), 10, (int)
    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

    // Header Row
    $sheetXml .= '<row r="1">';
    for ($i = 0; $i < 7; $i++)
        $sheetXml .= '<c r="' . chr(65 + $i) . '1" t="s"><v>' . $i . '</v></c>';
    $sheetXml .= '</row>';

    // Sample Data Row 1 (Samsung A50)
    $sheetXml .= '<row r="2">';
    $sheetXml .= '<c r="A2" t="s"><v>7</v></c>'; // Samsung
    $sheetXml .= '<c r="B2" t="s"><v>8</v></c>'; // A50
    $sheetXml .= '<c r="C2" t="s"><v>9</v></c>'; // Incell
    $sheetXml .= '<c r="D2"><v>15.00</v></c>'; // costo
    $sheetXml .= '<c r="E2"><v>25.00</v></c>'; // precio_venta
    $sheetXml .= '<c r="F2" t="s"><v>10</v></c>'; // pantalla
    $sheetXml .= '<c r="G2"><v>10</v></c>'; // stock
    $sheetXml .= '</row>';

    $sheetXml .= '</sheetData></worksheet>';
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

    $zip->close();
    echo "Plantilla XLSX generada con éxito.";
} else {
    echo "Falla al crear el archivo XLSX.";
}
