<?php

namespace AC\TcpdfBundle\Pdf;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
// use Symfony\Component\PropertyAccess\PropertyAccess;
use TCPDF;

class PdfBuider extends TCPDF
{
    protected $accessor;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        // $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    // =========================================================
    //  Table
    // =========================================================

    public function writeTable()
    {
        // Si version array
        // * pour l'entete
        //  head =>
        //      'Titre 1',
        //      'Titre 2',
        //      ['text'=> 'Titre 3', 'style' => 'text-align: right'],
        //  ]
        // * pour le body utiliser le propertyAccessor
        //  -- pour un tableau
        //  body => [ $row, [
        //      '[cle1]',
        //      '[cle2]',
        //      '[cle3]',
        //  ]]
        //  -- pour une collection (objet)
        //  body => [ $collection, [
        //      'propriete1',
        //      'propriete2',
        //      'propriete3',
        //  ]]
        // * un truc pour les totaux
        //  somme => [ $collection, [
        //      ['colspan' => 2, 'text' => 'Total'],    // si array => traitement
        //      'propriete3',                           // sinon sum de la colonne
        //  ]]
        // * un truc pour ajouter un foot : texte uniquement (sous les totaux ou a la place des totaux)
        //  -- pour un tableau
        //  body => [ $row, [
        //      'texte 1',
        //      'texte 2',
        //      'texte 3',
        //  ]]
    }


    // =========================================================
    //  Output
    // =========================================================

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
     * @return bool TRUE is PDF successfully saved; FALSE otherwise.
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
        return $binary = $this->Output('', 'S');
    }
}
