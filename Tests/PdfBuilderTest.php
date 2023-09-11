<?php

/*
 * This file is part of the ACTcpdfBundle package.
 *
 * (c) acucchieri <https://github.com/acucchieri>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AC\TcpdfBundle\Tests;

use AC\TcpdfBundle\Pdf\PdfBuilder;
use PHPUnit\Framework\TestCase;

class PdfBuilderTest extends TestCase
{
    public function testInline()
    {
        $pdf = new PdfBuilder();
        $response = $pdf->inline('my.pdf');
        $this->assertEquals('application/pdf',
            $response->headers->get('content-type')
        );
        $this->assertContains(
            $response->headers->get('content-disposition'),
            ['inline; filename=my.pdf', 'inline; filename="my.pdf"']
        );
    }

    public function testDownload()
    {
        $pdf = new PdfBuilder();
        $response = $pdf->download('my.pdf');

        $this->assertEquals('application/pdf',
            $response->headers->get('content-type')
        );
        $this->assertContains(
            $response->headers->get('content-disposition'),
            ['attachment; filename=my.pdf', 'attachment; filename="my.pdf"']
        );
    }

    public function testSave()
    {
        @mkdir($tmpdir = sys_get_temp_dir().'/actcpdf');
        $filename = sprintf('%s.pdf', tempnam($tmpdir, 'my'));

        $pdf = new PdfBuilder();
        $pdf->save($filename);

        $this->assertFileExists($filename);
    }

    public function testAttachment()
    {
        $pdf = new PdfBuilder();
        $str = $pdf->attachment('my.pdf');

        $this->assertNotEmpty($str);
        $this->assertMatchesRegularExpression('/Content-Transfer-Encoding: base64/', $str);
    }

    public function testToString()
    {
        $pdf = new PdfBuilder();
        $str = $pdf->toString();

        $this->assertNotEmpty($str);
    }

    public function testAddMultiCellRow()
    {
        $this->expectNotToPerformAssertions();
        $pdf = new PdfBuilder();
        $pdf->AddPage();
        $pdf->addMultiCellRow([
            ['FOOBAR', ['width' => 50]],
        ]);
    }
}
