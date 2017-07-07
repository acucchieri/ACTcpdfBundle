<?php

/*
 * This file is part of the ACTcpdfBundle package.
 *
 * (c) acucchieri <http://acucchieri.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AC\TcpdfBundle\Pdf;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TCPDF;

class PdfBuider extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }

    /**
     * Send the PDF inline to the browser.
     *
     * @param string $filename The file name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inline($filename = 'doc.pdf')
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download($filename = 'doc.pdf')
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
    public function save($filename = 'doc.pdf')
    {
        $this->Output($filename, 'F');

        return file_exists($filename);
    }

    /**
     * Return the PDF as base64 mime multi-part email attachment (RFC 2045).
     *
     * @param string $filename The file name
     *
     * @return string
     */
    public function attachment($filename = 'doc.pdf')
    {
        return $this->Output($filename, 'E');
    }

    /**
     * Return the PDF as a string.
     *
     * @return string
     */
    public function toString()
    {
        return $this->Output('', 'S');
    }
}
