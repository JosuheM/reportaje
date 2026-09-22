<?php
/**
 * Importa a la tabla `reportaje` los artículos que ya están en HTML
 * (los 9 enlazados desde reportajes-1.html). Ejecutar UNA vez por consola:
 *   C:\xampp\php\php.exe admin\importar-reportajes.php
 * Vuelve a ejecutarse sin duplicar (salta los slugs ya existentes).
 * Borra este archivo cuando termines.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Solo por consola.\n"); }
require_once __DIR__ . '/config/conexion.php';

$raiz = __DIR__ . '/..';
$listado = @file_get_contents("$raiz/reportajes-1.html");
if ($listado === false) { exit("No encuentro reportajes-1.html\n"); }

$meses = ['ene'=>1,'feb'=>2,'mar'=>3,'abr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'ago'=>8,'set'=>9,'sep'=>9,'oct'=>10,'nov'=>11,'dic'=>12];

libxml_use_internal_errors(true);

/** DOMDocument a partir de un fragmento/página UTF-8. */
function dom(string $html): DOMDocument {
    $d = new DOMDocument();
    $d->loadHTML('<?xml encoding="utf-8"?>' . $html);
    return $d;
}
function inner_html(DOMNode $n): string {
    $h = '';
    foreach ($n->childNodes as $c) { $h .= $n->ownerDocument->saveHTML($c); }
    return trim($h);
}

// --- 1. tarjetas de reportajes-1.html ---
$doc = dom($listado);
$xp  = new DOMXPath($doc);
$tarjetas = [];
foreach ($xp->query('//div[contains(@class,"grids5-info")]') as $card) {
    $a   = $xp->query('.//a[contains(@class,"d-block")]', $card)->item(0);
    $img = $xp->query('.//img', $card)->item(0);
    $h5  = $xp->query('.//h5', $card)->item(0);
    if (!$a) continue;
    $href = $a->getAttribute('href');
    if (!preg_match('/\.html$/', $href) || preg_match('/^(index|reportajes-\d)/', $href)) continue;
    $tarjetas[$href] = [
        'slug'    => $href,
        'portada' => $img ? basename($img->getAttribute('src')) : null,
        'fecha_txt' => $h5 ? trim($h5->textContent) : '',
    ];
}

echo "Tarjetas encontradas: " . count($tarjetas) . "\n";

$insert = db()->prepare(
    'INSERT INTO reportaje
        (reportaje_titulo, autor, reportaje_fecha_publicacion, resumen_corto, descripcion_detallada,
         portada, slug, origen, destacado, usuario_id, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, "import", ?, NULL, NOW(), NOW())'
);
$existe = db()->prepare('SELECT reportaje_id FROM reportaje WHERE slug = ?');

$n = 0; $orden = 0;
foreach ($tarjetas as $t) {
    $orden++;
    $existe->execute([$t['slug']]);
    if ($existe->fetch()) { echo "  = ya existe: {$t['slug']}\n"; continue; }

    $file = "$raiz/{$t['slug']}";
    if (!is_file($file)) { echo "  ! falta el archivo: {$t['slug']}\n"; continue; }
    $art = dom(file_get_contents($file));
    $x2  = new DOMXPath($art);

    $h = $x2->query('//h2[contains(@class,"title-single")]')->item(0);
    $titulo = $h ? trim($h->textContent) : $t['slug'];

    $cont = $x2->query('//div[contains(@class,"single-post-content")]')->item(0);
    $bajada = ''; $autor = ''; $cuerpo = '';
    if ($cont) {
        $q = $x2->query('.//blockquote//q', $cont)->item(0);
        $bajada = $q ? trim(preg_replace('/\s+/', ' ', $q->textContent)) : '';
        // quitar el blockquote del cuerpo
        foreach (iterator_to_array($x2->query('.//blockquote', $cont)) as $bq) {
            $bq->parentNode->removeChild($bq);
        }
        // autor: "Por XXX" al inicio del primer <p>, hasta el primer <br>
        $p1 = $x2->query('.//p', $cont)->item(0);
        if ($p1) {
            $frag = inner_html($p1);
            $primera = preg_split('/<br\s*\/?>/i', $frag)[0];
            $primera = trim(strip_tags($primera));
            if (preg_match('/^Por\s+.{2,80}$/u', $primera)) {
                $autor = $primera;
            }
        }
        $cuerpo = inner_html($cont);
    }

    // fecha "Ago 18, 2026"
    $fecha = null;
    if (preg_match('/([A-Za-zÁÉÍÓÚáéíóú]{3})\s+(\d{1,2}),\s*(\d{4})/u', $t['fecha_txt'], $m)) {
        $mm = $meses[mb_strtolower($m[1])] ?? 1;
        $fecha = sprintf('%04d-%02d-%02d 00:00:00', (int)$m[3], $mm, (int)$m[2]);
    }

    $destacado = $orden <= 3 ? '1' : null;   // los 3 primeros, destacados en portada
    $insert->execute([$titulo, $autor, $fecha, $bajada, $cuerpo, $t['portada'], $t['slug'], $destacado]);
    $n++;
    echo "  + importado: {$titulo}\n";
}

echo "\nImportados nuevos: $n\n";
echo "Total en tabla reportaje: " . db()->query('SELECT COUNT(*) FROM reportaje')->fetchColumn() . "\n";
