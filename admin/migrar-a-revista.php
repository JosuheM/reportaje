<?php
/**
 * Copia el contenido de la base vieja (bdreportaje) a la nueva (revista_digital).
 * Ejecutar UNA vez por consola:  C:\xampp\php\php.exe admin\migrar-a-revista.php
 * No duplica: si la tabla destino ya tiene filas, la salta.
 * Borra este archivo cuando termines.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Solo por consola.\n"); }

function pdo(string $bd): PDO {
    return new PDO("mysql:host=localhost;dbname=$bd;charset=utf8mb4", 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
$old = pdo('bdreportaje');
$new = pdo('revista_digital');

function vacia(PDO $db, string $t): bool {
    return (int) $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() === 0;
}

/* ---------- 1. usuarios ---------- */
if (vacia($new, 'usuarios')) {
    $u = $old->query('SELECT * FROM usuario')->fetchAll();
    $ins = $new->prepare('INSERT INTO usuarios (id, nombres, ap_paterno, ap_materno, email, password_hash, rol, created_at)
                          VALUES (?,?,?,?,?,?,?,NOW())');
    foreach ($u as $r) {
        $rol = in_array($r['rol'], ['admin','editor','redactor'], true) ? $r['rol'] : 'admin';
        $ins->execute([$r['usuario_id'], $r['usuario_nombres'], $r['usuario_apellido_paterno'] ?: '-',
                       $r['usuario_apellido_materno'], $r['usuario_email'], $r['usuario_contrasena'], $rol]);
    }
    echo "usuarios: " . count($u) . "\n";
} else { echo "usuarios: ya tenía datos, saltado\n"; }

/* ---------- 2. autores (Redacción + los que aparezcan en reportaje.autor) ---------- */
$autorId = [];   // texto -> id
function autor_de(PDO $new, array &$cache, ?string $texto): int {
    $texto = trim((string) $texto);
    if ($texto === '') { $texto = 'Redacción'; }
    // limpiar prefijo "Por "
    $limpio = preg_replace('/^\s*Por\s+/iu', '', $texto);
    $limpio = trim($limpio) ?: 'Redacción';
    if (isset($cache[$limpio])) return $cache[$limpio];
    $q = $new->prepare('SELECT id FROM autores WHERE nombres = ? LIMIT 1');
    $q->execute([$limpio]);
    $id = $q->fetchColumn();
    if (!$id) {
        $esNick = ($limpio === 'Redacción') ? 1 : 0;
        $new->prepare('INSERT INTO autores (nombres, es_nickname) VALUES (?, ?)')->execute([$limpio, $esNick]);
        $id = (int) $new->lastInsertId();
    }
    return $cache[$limpio] = (int) $id;
}

/* ---------- 3. reportajes ---------- */
if (vacia($new, 'reportajes')) {
    $rs = $old->query('SELECT * FROM reportaje ORDER BY orden ASC, reportaje_id ASC')->fetchAll();
    $ins = $new->prepare('INSERT INTO reportajes
        (titulo, slug, resumen_corto, desarrollo, foto_principal, fecha_publicacion, es_destacado, orden, origen, autor_id, usuario_id, created_at, updated_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())');
    $uid = (int) $new->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetchColumn();
    $n = 0;
    foreach ($rs as $r) {
        $aid = autor_de($new, $autorId, $r['autor'] ?? '');
        $cuerpo = (string) $r['descripcion_detallada'];
        // en los importados, quitar el "Por XXX<br>" que abre el primer <p> (ya va como autor)
        if (($r['origen'] ?? '') === 'import') {
            $cuerpo = preg_replace('/(<p[^>]*>)\s*Por\s+[^<]{2,80}?<br\s*\/?>/iu', '$1', $cuerpo, 1);
        }
        $fecha = $r['reportaje_fecha_publicacion'] ? substr($r['reportaje_fecha_publicacion'], 0, 10) : date('Y-m-d');
        $ins->execute([
            $r['reportaje_titulo'],
            $r['slug'] ?: null,
            $r['resumen_corto'] !== null ? mb_substr($r['resumen_corto'], 0, 500) : null,
            $cuerpo !== '' ? $cuerpo : '<p></p>',
            $r['portada'] ?: null,
            $fecha,
            $r['destacado'] ? 1 : 0,
            (int) $r['orden'],
            $r['origen'] ?: 'panel',
            $aid,
            $r['usuario_id'] ? (int) $r['usuario_id'] : $uid,
        ]);
        $n++;
    }
    echo "reportajes: $n  (autores creados: " . count($autorId) . ")\n";
} else { echo "reportajes: ya tenía datos, saltado\n"; }

/* ---------- 4. noticias ---------- */
if (vacia($new, 'noticias')) {
    $rs = $old->query('SELECT * FROM noticia_reciente ORDER BY noticia_reciente_fecha_publicacion DESC, noticia_reciente_id ASC')->fetchAll();
    $uid = (int) $new->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetchColumn();
    $ins = $new->prepare('INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, orden, usuario_id)
                          VALUES (?,?,?,?,?,?)');
    $ord = 0;
    foreach ($rs as $r) {
        $ord += 10;
        $fecha = $r['noticia_reciente_fecha_publicacion'] ? substr($r['noticia_reciente_fecha_publicacion'],0,10) : date('Y-m-d');
        $ins->execute([$r['noticia_reciente_titulo'], $r['portada'] ?: null, $r['noticia_reciente_url'] ?: null,
                       $fecha, $ord, $r['usuario_id'] ? (int) $r['usuario_id'] : $uid]);
    }
    echo "noticias: " . count($rs) . "\n";
} else { echo "noticias: ya tenía datos, saltado\n"; }

/* ---------- 5. boletines ---------- */
if (vacia($new, 'boletines')) {
    $rs = $old->query('SELECT * FROM boletin_ntep ORDER BY orden ASC, boletin_ntep_id ASC')->fetchAll();
    $uid = (int) $new->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetchColumn();
    $ins = $new->prepare('INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, orden, usuario_id)
                          VALUES (?,?,?,?,?,?,?)');
    $vistos = [];
    foreach ($rs as $r) {
        $num = trim($r['boletin_ntep_numero'] ?? '') ?: ('s/n ' . $r['boletin_ntep_id']);
        while (isset($vistos[$num])) { $num .= '-b'; }   // numero_boletin es UNIQUE
        $vistos[$num] = true;
        $fecha = $r['boletin_ntep_fecha_publicacion'] ? substr($r['boletin_ntep_fecha_publicacion'],0,10) : date('Y-m-d');
        $ins->execute([$num, $r['boletin_ntep_resumen'] ?: null, $r['portada'] ?: null,
                       $r['archivo'] ?: 'sin-archivo.pdf', $fecha, (int) ($r['orden'] ?? 0),
                       $r['usuario_id'] ? (int) $r['usuario_id'] : $uid]);
    }
    echo "boletines: " . count($rs) . "\n";
} else { echo "boletines: ya tenía datos, saltado\n"; }

/* ---------- 6. config ---------- */
if (vacia($new, 'config')) {
    $rs = $old->query('SELECT * FROM config')->fetchAll();
    $ins = $new->prepare('INSERT INTO config (clave, valor, updated_at) VALUES (?,?,NOW())');
    foreach ($rs as $r) { $ins->execute([$r['clave'], $r['valor']]); }
    echo "config: " . count($rs) . "\n";
} else { echo "config: ya tenía datos, saltado\n"; }

echo "\n=== LISTO. Revisa revista_digital y borra este archivo. ===\n";
