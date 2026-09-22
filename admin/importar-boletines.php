<?php
/**
 * Importa a la tabla `boletin_ntep` los 6 boletines que ya están en boletines.html
 * (con sus PDF en /boletines y sus portadas en /assets/images).
 * Ejecutar UNA vez:  C:\xampp\php\php.exe admin\importar-boletines.php
 * No duplica (salta los que ya tienen ese archivo). Borra este archivo al terminar.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Solo por consola.\n"); }
require_once __DIR__ . '/config/conexion.php';

$raiz = __DIR__ . '/..';
$html = @file_get_contents("$raiz/boletines.html");
if ($html === false) { exit("No encuentro boletines.html\n"); }

$meses = ['ene'=>1,'feb'=>2,'mar'=>3,'abr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'ago'=>8,'set'=>9,'sep'=>9,'oct'=>10,'nov'=>11,'dic'=>12];

libxml_use_internal_errors(true);
$d = new DOMDocument();
$d->loadHTML('<?xml encoding="utf-8"?>' . $html);
$xp = new DOMXPath($d);

$existe = db()->prepare('SELECT boletin_ntep_id FROM boletin_ntep WHERE archivo = ?');
$ins = db()->prepare(
    'INSERT INTO boletin_ntep
        (boletin_ntep_numero, boletin_ntep_fecha_publicacion, boletin_ntep_resumen, portada, archivo,
         usuario_id, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, NULL, NOW(), NOW())'
);

$n = 0;
foreach ($xp->query('//div[contains(@class,"grids5-info")]') as $card) {
    $a   = $xp->query('.//a[contains(@class,"d-block")]', $card)->item(0);
    $img = $xp->query('.//img', $card)->item(0);
    $h5  = $xp->query('.//h5', $card)->item(0);
    if (!$a) continue;

    $pdfHref = $a->getAttribute('href');                 // boletines/boletin-NTEP-edicion-N45-2808.pdf
    if (stripos($pdfHref, '.pdf') === false) continue;
    $archivo = basename($pdfHref);
    $portada = $img ? basename($img->getAttribute('src')) : null;

    $existe->execute([$archivo]);
    if ($existe->fetch()) { echo "  = ya existe: $archivo\n"; continue; }

    // número: "N45" dentro del nombre del archivo -> "Nº 45"
    $numero = preg_match('/-N(\d+)-/i', $archivo, $m) ? 'Nº ' . $m[1] : $archivo;

    // fecha: "Ago 28, 2025"
    $fecha = null;
    if ($h5 && preg_match('/([A-Za-zÁÉÍÓÚáéíóú]{3})\s+(\d{1,2}),\s*(\d{4})/u', $h5->textContent, $mm)) {
        $k = $meses[mb_strtolower($mm[1])] ?? 1;
        $fecha = sprintf('%04d-%02d-%02d 00:00:00', (int) $mm[3], $k, (int) $mm[2]);
    }

    $ins->execute([$numero, $fecha, '', $portada, $archivo]);
    $n++;
    echo "  + importado: $numero  ($archivo)\n";
}

echo "\nImportados nuevos: $n\n";
echo "Total en boletin_ntep: " . db()->query('SELECT COUNT(*) FROM boletin_ntep')->fetchColumn() . "\n";
