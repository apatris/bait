<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\components\widgets\LanguageSwitcher;
use app\components\widgets\PaceWidget;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Proposal;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-blue">
<?php $this->beginBody() ?>

<header>

    <!-- Product Logo -->
    <a href="<?= Url::to(['/'])?>" class="logo hidden-xs">
                <span class="icon">
                    <i class="fa fa-cube"></i>
                </span>
        Bait
    </a>
    <!-- End Product Logo -->

    <!-- Header Navigation -->
    <nav class="navbar-main" role="navigation">

        <?php if(!Yii::$app->user->isGuest) { ?>

            <!-- Left Button Container -->
            <ul class="button-container pull-left">

                <li class="item">
                    <!-- Left Sidebar Toggle Button -->
                    <a id="sidebarLeftToggle" class="nav-button" data-toggle="collapse" data-target=".sidebarLeft">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="fa fa-bars"></span>
                    </a>
                </li>

            </ul>
            <!-- End Left Button Container -->

        <?php } ?>


        <ul class="button-container pull-right">

            <?php if(Yii::$app->user->isGuest) { ?>

                <li class="item">
                    <!-- Right Sidebar Toggle Button -->
                    <a href="<?= Url::to(['/user/login']) ?>" class="nav-button">
                        <?= Yii::t('app', 'Login') ?>
                    </a>
                    <!-- End Right Sidebar Toggle Button -->
                </li>

            <?php } else { ?>

                <li class="item">
                    <!-- Right Sidebar Toggle Button -->
                    <a href="<?= Url::to(['/invited-tree']) ?>" class="nav-button">
                        <?= Yii::$app->user->identity->username ?>
                    </a>
                    <!-- End Right Sidebar Toggle Button -->
                </li>

                <li class="item">
                    <!-- Right Sidebar Toggle Button -->
                    <a href="<?= Url::to(['/user/security/logout']) ?>" data-method="post" class="nav-button">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                    </a>
                    <!-- End Right Sidebar Toggle Button -->
                </li>

            <?php } ?>

            <li class="item">
                <?php
                echo Nav::widget([
                    'encodeLabels' => false,
                    'options' => ['class' => 'nav-button-flags'],
                    'items' => [
                        LanguageSwitcher::run()
                    ],
                ]);
                ?>
            </li>

        </ul>

        <!-- Navbar Content Center -->
        <div class="nav-content">
            <!-- Page Title -->
            <h3 class="page-title">
                <?= $this->title ?>
            </h3>
            <!-- End Page Title -->
        </div>
        <!-- End Navbar Content Center -->

    </nav>
    <!-- End Header Navigation -->

</header>

<aside class="sidebar sidebar-left navbar-collapse sidebarLeft collapse in">

    <div class="sidebar-wrapper">

        <!-- Sidebar Navigation Wrapper -->

        <ul class="sidebar-nav">

            <!-- Menu Item -->
            <?php if (Yii::$app->user->can('activateProfiles')) { ?>
                <li class="border-left-green">
                    <a href="<?= Url::to(['/proposals']) ?>" title="Dashboard">
                        <?= '<i class="menu-icon fa fa-lg fa-bell-o"></i>' . Yii::t('app', 'Proposals') .
                        (!empty(Proposal::getCount()) ? '<span class="badge bg-light-green pull-right">' . Proposal::getCount() . '</span>' : '') ?>
                    </a>
                </li>
            <?php } ?>
            <!-- End Menu Item -->

            <!-- Menu Item -->
            <?php if (!Yii::$app->user->isGuest) { ?>
                <li class="border-left-purple">
                    <a href="<?= Url::to(['/invited-tree']) ?>" title="Dashboard">
                        <?= '<i class="menu-icon fa fa-lg fa-users"></i>' . Yii::t('app', 'Invited') ?>
                    </a>
                </li>
            <?php } ?>
            <!-- End Menu Item -->

            <?php if (Yii::$app->user->can('viewAdminPanel')) { ?>
            <!-- Menu Item -->
            <li class="border-left-red">
                <!-- 'data-target' attribute must mactch the id of the submenu dropdown -->
                <a class="collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#ui-elements-submenu" title="UI Elements">
                    <i class="menu-icon fa fa-lg fa-fw fa-desktop"></i> <span><?= Yii::t('app', 'Admin') ?></span>
                    <i class="fa fa-caret-right submenu-indicator"></i>
                </a>
                <!-- Sub Menu Item -->
                <ul id="ui-elements-submenu" class="collapse">
                    <!-- Menu Item -->
                    <?php if (Yii::$app->user->can('manageUsers')) { ?>
                    <li>
                        <a href="<?= Url::to(['/user/admin']) ?>"><?= Yii::t('app', 'Users') ?></a>
                    </li>
                    <?php } ?>
                    <!-- End Menu Item -->
                    <!-- Menu Item -->
                    <?php if (Yii::$app->user->can('manageTranslations')) { ?>
                    <li>
                        <a href="<?= Url::to(['/translations']) ?>"><?= Yii::t('app', 'Translations') ?></a>
                    </li>
                    <?php } ?>
                    <!-- End Menu Item -->
                    <!-- Menu Item -->
                    <?php if (Yii::$app->user->can('changeForms')) { ?>
                    <li>
                        <a class="collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#icons-submenu">
                            <?= Yii::t('app', 'Dynamic form') ?>
                            <i class="fa fa-caret-right submenu-indicator"></i>
                        </a>
                        <ul id="icons-submenu" class="collapse">
                            <!-- Menu Item -->
                            <li>
                                <a href="<?= Url::to(['/form']) ?>"><?= Yii::t('app', 'Change') ?></a>
                            </li>
                            <!-- End Menu Item -->
                            <!-- Menu Item -->
                            <li>
                                <a href="<?= Url::to(['/form/view']) ?>"><?= Yii::t('app', 'View') ?></a>
                            </li>
                            <!-- End Menu Item -->
                        </ul>
                    </li>
                    <?php } ?>
                    <!-- End Menu Item -->

                </ul>
            </li>
            <!-- End Menu Item -->
            <?php } ?>

        </ul>
    </div>

</aside>

<aside class="content-wrapper collapse in sidebarLeft">

    <!-- Page Content -->
    <div class="content container-fluid sidebarRight animated fadeInUp mail message-list-wrapper">

        <?= !Yii::$app->user->isGuest ? Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'options' => [
                'class' => 'breadcrumb breadcrumb-theme breadcrumb-sm breadcrumb-blue'
            ]
        ]) : '' ?>

        <?= $content ?>

    </div>
    <!-- End Page Content -->
</aside>

<?= PaceWidget::widget([
    'color' => 'white',
    'theme' => 'flash',
]); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
