<?php
/** Listado público de Podcast, dinámico desde la base. */
require_once __DIR__ . '/admin/config/conexion.php';
require_once __DIR__ . '/inc/publico.php';

$podcasts = db()->query(
    'SELECT id, titulo, url_embed, fecha_publicacion
     FROM podcasts
     ORDER BY orden ASC, id ASC'
)->fetchAll();

frente_header('DDP Noticias | Podcast', 'podcast');
?>
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
            <h2 class="title-big">Podcast</h2>
            <div class="breadcrumb"><ul>
                <li><a href="index.php">Inicio</a></li>
                <li class="active"> Podcast</li>
            </ul></div>
        </div></div></div>
    </div>
</section>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-4">
        <div class="row">
            <?php if (!$podcasts): ?>
                <div class="col-12 text-center text-muted py-5">Aún no hay episodios publicados.</div>
            <?php endif; ?>
            <?php foreach ($podcasts as $pod): ?>
            <div class="col-lg-4 col-md-6 mb-5" id="p<?= (int) $pod['id'] ?>">
                <div class="area-box">
                    <div class="ratio-16x9 mb-2"><?= embed_media($pod['url_embed']) ?></div>
                    <h5 class="mt-2"><?= h($pod['titulo']) ?></h5>
                    <?php if ($pod['fecha_publicacion']): ?>
                        <span class="text-muted small"><?= h(fecha_larga($pod['fecha_publicacion'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php frente_footer(); ?>
