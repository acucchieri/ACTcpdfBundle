<?php

/*
 * This file is part of the ACTcpdfBundle package.
 *
 * (c) acucchieri <https://github.com/acucchieri>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AC\TcpdfBundle\Pdf;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TCPDF;

class PdfBuilder extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }

    /**
     * Add a row of MultiCell.
     *
     * @param array $cells      The Cells. Each Cell is an array (text and options)
     *    [
     *        [$data['foo'], ['width' => 50]],
     *        [$data['bar'], ['width' => 30, 'align' => 'C']],
     *    ]
     * @param bool  $sameHeight If TRUE all the row cells have the same height
     * @param bool  $nobr       If TRUE the row is not split accross two pages
     */
    public function addMultiCellRow(array $cells, bool $sameHeight = false, bool $nobr = false)
    {
        $startPage = $this->getPage();
        $startY = $this->GetY();
        $lastCell = count($cells) - 1;
        $cellsY = [];
        $cellsH = null;  // used if $sameHeight=true

        $getCellOptions = function (array $cell) {
            $opts = (isset($cell[1])) ?
                (array) $cell[1] :
                []
            ;

            return array_replace([
                'height' => 0,
                'width' => 0,
                'border' => 0,      // 0, 1
                'align' => 'L',     // L, C, R, J
                'valign' => 'T',    // T, M, B
                'fill' => false,
                'is_html' => false,
            ], $opts);
        };

        $setCellsY = function ($page, $y) use (&$cellsY) {
            $page = $this->getPage();
            $y = $this->GetY();
            if (!isset($cellsY[$page])) {
                $cellsY[$page] = $y;
            } else {
                if ($y > $cellsY[$page]) {
                    $cellsY[$page] = $y;
                }
            }
        };

        if (true === $sameHeight) {
            // estimated text height
            $p = $this->getCellPaddings();
            $this->SetCellPadding(0);
            $textHeight = $this->getStringHeight(0, 'ABC123', false, true, '', 1);
            $this->setCellPaddings($p['L'], $p['T'], $p['R'], $p['B']);
            // dry run (count max lines in the cells)
            $liCount = 1;
            $this->startTransaction();
            foreach ($cells as $i => $cell) {
                $opts = $getCellOptions($cell);
                $ln = ($lastCell === $i) ? 1 : 2;
                $n = $this->MultiCell($opts['width'], $textHeight, $cell[0], $opts['border'], $opts['align'],
                    false, $ln, $this->GetX(), $startY, true, 0,
                    $opts['is_html'], true, 0, $opts['valign'], false
                );
                $liCount = ($n > $liCount) ? $n : $liCount;
                $this->setPage($startPage);
            }
            $this->rollbackTransaction(true);
            // row height
            $cellsH = ($liCount * $textHeight) + ($p['T'] + $p['B']);
        }

        if ($nobr === true) {
            if ($this->checkPageBreak($cellsH, $startY, false)) {
                $this->addPage();
                $this->setY($this->getMargins()['top']);
                $startPage = $this->getPage();
                $startY = $this->GetY();
            }
        }

        foreach ($cells as $i => $cell) {
            if (!empty($cell) && null === $cell[0]) {
                $cell[0] = '';
            }
            if (!isset($cell[0]) || !is_scalar($cell[0])) {
                throw new \Exception('First element in cell array must be a scalar');
            }
            // cell text
            $text = $cell[0];
            // cell options
            $opts = $getCellOptions($cell);
            $reseth = true;
            $stretch = 0;
            $fitcell = false;
            $autopadding = true;
            $h = (true === $sameHeight) ? $cellsH : $opts['height'];
            $maxh = 0;
            $ln = ($lastCell === $i) ? 1 : 2;

            $this->MultiCell(
                $opts['width'],
                $h,
                $text,
                $opts['border'],
                $opts['align'],
                $opts['fill'],
                $ln,
                $this->GetX(),
                $startY,
                $reseth,
                $stretch,
                $opts['is_html'],
                $autopadding,
                $maxh,
                $opts['valign'],
                $fitcell
            );

            $setCellsY($this->getPage(), $this->GetY());

            // Revert to page where the row started (to print the next cell if any).
            $this->setPage($startPage);
        }

        $newPage = max(array_keys($cellsY));
        $newY = $cellsY[$newPage];

        $this->setPage($newPage);
        $this->SetXY($this->GetX(), $newY);
    }

    /**
     * Send the PDF inline to the browser.
     *
     * @param string $filename The file name
     *
     * @return Response
     */
    public function inline(string $filename = 'doc.pdf'): Response
    {
        $binary = $this->toString();

        $response = new Response($binary);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    /**
     * Send the PDF to the browser and force a file download.
     *
     * @param string $filename The file name
     *
     * @return Response
     */
    public function download(string $filename = 'doc.pdf'): Response
    {
        $binary = $this->toString();

        $response = new Response($binary);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    /**
     * Save PDF document.
     *
     * @param string $filename Fully qualified file name
     *
     * @return bool TRUE is PDF successfully saved; FALSE otherwise
     */
    public function save(string $filename = 'doc.pdf'): bool
    {
        $this->Output($filename, 'F');

        return file_exists($filename);
    }

    /**
     * Return the PDF as base64 mime multi-part email attachment (RFC 2045).
     *
     * @param string $filename The file name
     */
    public function attachment(string $filename = 'doc.pdf'): string
    {
        return $this->Output($filename, 'E');
    }

    /**
     * Return the PDF as a string.
     */
    public function toString(): string
    {
        return $this->Output('', 'S');
    }
}
