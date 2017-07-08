<?php

namespace AC\TcpdfBundle\Tests;

use AC\TcpdfBundle\Pdf\PdfBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PdfBuilderTest extends TestCase
{
    public function testInline()
    {
        $pdf = new PdfBuilder();
        $response = $pdf->inline('my.pdf');

        $this->assertEquals('application/pdf',
            $response->headers->get('content-type')
        );
        $this->assertEquals('inline; filename="my.pdf"',
            $response->headers->get('content-disposition')
        );
    }

    public function testDownload()
    {
        $pdf = new PdfBuilder();
        $response = $pdf->download('my.pdf');

        $this->assertEquals('application/pdf',
            $response->headers->get('content-type')
        );
        $this->assertEquals('attachment; filename="my.pdf"',
            $response->headers->get('content-disposition')
        );
    }

    public function testSave()
    {
        @mkdir($tmpdir = sys_get_temp_dir().'/actcpdf');
        $filename = sprintf('%s.pdf', tempnam($tmpdir, 'my'));

        $pdf = new PdfBuilder();
        $str = $pdf->save($filename);

        $this->assertFileExists($filename);
    }

    public function testAttachment()
    {
        $pdf = new PdfBuilder();
        $str = $pdf->attachment('my.pdf');

        $this->assertNotEmpty($str);
        $this->assertRegexp('/Content-Transfer-Encoding: base64/', $str);
    }

    public function testToString()
    {
        $pdf = new PdfBuilder();
        $str = $pdf->toString('my.pdf');

        $this->assertNotEmpty($str);
    }
}
