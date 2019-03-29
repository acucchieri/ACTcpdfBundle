ACTcpdfBundle
=============

The `ACTcpdfBundle` integrates the [TCPDF](https://github.com/tecnickcom/TCPDF) PHP library with Symfony. This means easy-to-implement and easy-to-ouptput PDF documents in your Symfony application.

Installation
------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require acucchieri/tcpdf-bundle
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.


### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the app/AppKernel.php file of your project:

``` php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new AC\TcpdfBundle\ACTcpdfBundle(),
        ];

        // ...
    }
}
```

Usage
-----

Use `AC\TcpdfBundle\Pdf\PdfBuider` to create your PDF document. This class extends TCPDF, see [TCPDF documentation](https://tcpdf.org) for more informations about PDF generation.


### PDF Output

`ACTcpdfBundle` provide helpers to serve your PDF

#### Display the PDF in the browser

Return a Response with `Content-Disposition: inline`

``` php
$myPdf->inline('my-pdf.doc');
```

#### Download the PDF

Return a Response with `Content-Disposition: attachment`

``` php
$myPdf->download('my-pdf.doc');
```

#### Get the PDF as base64 mime multi-part email attachment (RFC 2045)

``` php
$myPdf->attachment('my-pdf.doc');
```

#### Save the PDF on a filesystem

``` php
$myPdf->save('/path/to/my-pdf.doc');
```

#### Output the PDF as string

``` php
$myPdf->toString();
```

### MultiCell Helper

`AC\TcpdfBundle\Pdf\PdfBuilder::addMultiCellRow($cells, $sameHeight, $nobr)` allow you to build complex tables, based on `MultiCell`.

`$cells` is a multidimensional array. Each cell (array) contains :
 * The data (text or html)
 * The options.  Available options :
  * `height` Cell height
  * `width` Cell width
  * `border` Draw the cell borders. Allowed values : 0 or 1. Default = 0
  * `align` Horz alignment. Allowed values : 'L' (left), 'C' (center), 'R' (right) or 'J' (justify). Default = 'T'
  * `valign` Vert alignment. Allowed values 'T' (top), 'M' (middle) or 'B' (bottom). Default = 'T'
  * `fill` Indicates if the cell background must be painted. true or false. Default = false
  * `is_html` Indicate if the data is html. See TCDPF doc for the supported tags. Default = false

`$sameHeight` If set to `true` all the row cells have the same height. Default = false.

`$nobr` If set to `true` the row is not break across 2 pages. Default = false.

Example

``` php
$data = [
  ['foo' => 'AAA', 'bar' => 123, 'baz' => '<b>text<b>'],
  ['foo' => 'BBB', 'bar' => 456, 'baz' => '<a href="https://domain.td">link</a>'],
  ['foo' => 'CCC', 'bar' => 789, 'baz' => '<ul><li>line 1</li><li>line 2</li></ul>'],
];

foreach ($data as $row) {
    $pdf->addRowCell([
      [$row['foo'], ['with' => 30]],
      [$row['bar'], ['width' => 40, 'align' => 'R']],
      [$row['baz'], ['width' => 50, 'is_html' => true]],
    ]);
}
```


License
-------

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE)
