<?php
/**
 * Template pagination Bootstrap 5.
 *
 * Dipakai lewat $pager->links('default', 'shopease').
 * CI4 membangun URI halaman dari current_url(), sehingga parameter
 * pencarian (q, category, sort) tetap terbawa saat berpindah halaman.
 *
 * @var CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<?php if ($pager->getPageCount() > 1) : ?>
    <nav aria-label="Navigasi halaman" class="mt-4">
        <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $pager->hasPrevious() ? esc($pager->getPrevious(), 'attr') : '#' ?>"
                   aria-label="Halaman sebelumnya">&laquo;</a>
            </li>

            <?php foreach ($pager->links() as $link) : ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= esc($link['uri'], 'attr') ?>"><?= esc((string) $link['title']) ?></a>
                </li>
            <?php endforeach; ?>

            <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $pager->hasNext() ? esc($pager->getNext(), 'attr') : '#' ?>"
                   aria-label="Halaman berikutnya">&raquo;</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
