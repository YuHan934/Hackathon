<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

if (!empty($_POST['content'])) {
    $content = $_POST['content'];

    $dompdf = new Dompdf();
    $dompdf->loadHtml("<h2>Generated Documentation</h2><p>{$content}</p>");
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("documentation.pdf", ["Attachment" => 1]);
    exit;
} else {
    echo "No content to export.";
}
