<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\User\UserInfoRepository;

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
/* @var Concrete\Core\Form\Service\Form $form */
$form = $app->make('helper/form');
/* @var Concrete\Core\Validation\CSRF\Token $token */
$token = $app->make('helper/validation/token');
/* @var $dh \Concrete\Core\Localization\Service\Date */
$dh = $app->make('helper/date');
/** @var string $rated_date */
$rated_date = $rated_date ?? null;
?>
<style>
    .ratings-header-menu{
        position: absolute;
        top: 55px;
        /*right: 35px;*/
    }
    div#ccm-dashboard-content-inner{
        padding-top: 0 !important;
    }
    .ccm-header-search-form-input{
        display: inline-block;
    }
    ul.ccm-header-search-navigation {
        list-style: none;
        text-align: right;
        padding: 4px 0 0 0;
        margin:0 0 0 0
    }
</style>
<?php
    $config = $app->make('config');
    $codeVersion = $config->get('concrete.version');
    if (version_compare($codeVersion, '9.0.0', '>')) {
        $style = "style=width:346px;right:350px;";
    } else {
        $style = "style=right:35px;";
    }
?>
<div class="ratings-header-menu" <?php echo $style;?>>
    <form class="form-inline" action="<?php echo $view->action('search_ratings'); ?>">
        <?php echo $token->output('search_ratings'); ?>
        <div class="ccm-header-search-form-input">
            <?= $app->make('helper/form/date_time')->date('rated_date', $rated_date) ?>
        </div>

        <button class="btn btn-primary" type="submit"><?php echo t('Search'); ?></button>

        <ul class="ccm-header-search-navigation">
            <li>
                <a href="<?= $view->action('csv_export', $token->generate('export')) ?>?<?=$query?>" class="link-primary">
                    <i class="fa fa-download"></i><?php echo t('Export to CSV'); ?></a>
            </li>
        </ul>

    </form>
</div>
<div class="ccm-dashboard-content-full">
    <?php
    if (isset($ratingsList, $ratings) && count($ratings) > 0) {
        ?>

        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                <tr>
                    <th class="<?=$ratingsList->getSortClassName('r.cID')?>"><a href="<?=$ratingsList->getSortURL('r.cID')?>"><?= t('Page Name')?></a></th>
                    <th class="<?=$ratingsList->getSortClassName('r.uID')?>"><a href="<?=$ratingsList->getSortURL('r.uID')?>"><?= t('User')?></a></th>
                    <th class="<?=$ratingsList->getSortClassName('r.ratedAt')?>"><a href="<?=$ratingsList->getSortURL('r.ratedAt')?>"><?= t('Rated At')?></a></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($ratings as $rating) {
                    $page = \Concrete\Core\Page\Page::getByID($rating->getCID());
                    $ui = $app->make(UserInfoRepository::class)->getByID($rating->getUID());
                    if ($ui) {
                        $name = h($ui->getUserName());
                    } else {
                        $name = t('Unknown');
                    } ?>
                    <tr>
                        <td><?=h($page->getCollectionName())?></td>
                        <td><?=$name?></td>
                        <td><?=$dh->formatDateTime($rating->getRatedAt(), true, true); ?></td>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
        </div>
        <div class="ccm-search-results-pagination">
            <?php
            if (isset($pagination) && is_object($pagination)) {
                echo $pagination->renderView('dashboard');
            } ?>
        </div>
        <?php
    } else {
        echo "<p class='text-center'>" . t('No search results were found') . '</p>';
    }
    ?>
</div>
<script>
    $('#rated_date_pub').attr('Placeholder', "<?= t('Rated From') ?>");
</script>