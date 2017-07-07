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

`ACTcpdfBundle` provide helpers to serve your PDF :

### Display the PDF in the browser

Return a Response with `Content-Disposition: inline`

``` php
$myPdf->inline('my-pdf.doc');
```

### Download the PDF

Return a Response with `Content-Disposition: attachment`

``` php
$myPdf->download('my-pdf.doc');
```

### Get the PDF as base64 mime multi-part email attachment (RFC 2045)

``` php
$myPdf->attachment('my-pdf.doc');
```

### Save the PDF on a filesystem

``` php
$myPdf->save('/path/to/my-pdf.doc');
```

### Output the PDF as string

``` php
$myPdf->toString();
```

License
-------

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE)
