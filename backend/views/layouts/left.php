<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
use izyue\admin\components\MenuHelper;

AppAsset::register($this);

$menuRows = MenuHelper::getAssignedMenu(Yii::$app->user->id);

$route = Yii::$app->controller->getRoute();
$routeArray = explode('/', $route);
array_pop($routeArray);
$controllerName = implode('/', $routeArray);

$this->registerCssFile('@web/statics/css/slidebars.css', ['depends' => 'backend\assets\AppAsset']);

$style = <<<CSS
.weather-category ul li{width:23%;}
.weather-category ul li h5{font-size:14px;}
.twt-feed.green-bg{background: #a9d86e}
.twt-feed.red-bg{background: #ff6c60}
.panel{
    margin-bottom: 0;
}
.panel h5{
    margin: 0;
    padding: 0;
}
.panel .weather-category{
    margin-top:0px;
    padding:5px 0;
}
.twt-category{
    margin-bottom: 0;
}
CSS;
$this->registerCss($style);

$loading = '<img src="/statics/img/loading.gif" width="100%">';

$script = <<<JS
    $("#get-account").click(function(){
         $("#special-accounts").html('$loading');
       $.get('/account/get-special-account',function(data){
            $("#special-accounts").html(data);
       })
    });
JS;
$this->registerJs($script);

function isSubUrl($menuArray, $route)
{

    if (isset($menuArray) && is_array($menuArray)) {

        if (isset($menuArray['items'])) {
            foreach ($menuArray['items'] as $item) {
                if (isSubUrl($item, $route)) {
                    return true;
                }
            }
        } else {
            $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];

            if ($url == '/' . $route) {
                return true;
            }
        }
    } else {
        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
        if ($url == '/' . $route) {
            return true;
        }
    }

    return false;

}

function isSubMenu($menuArray, $controllerName)
{

    if (isset($menuArray) && is_array($menuArray)) {

        if (isset($menuArray['items'])) {
            foreach ($menuArray['items'] as $item) {
                if (isSubMenu($item, $controllerName)) {
                    return true;
                }
            }
        } else {
            $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
            if (strpos($url, '/' . $controllerName . '/') !== false) {
                return true;
            }
        }
    } else {
        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
        if (strpos($url, '/' . $controllerName . '/') !== false) {
            return true;
        }
    }
    return false;

}


function initMenu($menuArray, $controllerName, $isSubUrl, $isShowIcon = false)
{
    if (isset($menuArray) && is_array($menuArray)) {

        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];

        if (empty($isSubUrl)) {
            $isSubMenu = isSubMenu($menuArray, $controllerName);
        } else {
            $route = Yii::$app->controller->getRoute();
            $isSubMenu = isSubUrl($menuArray, $route);
        }
        if ($isSubMenu) {
            $class = ' active ';
        } else {
            $class = '';
        }


        if (empty($menuArray['data']['hide'])) {
            if (isset($menuArray['items'])) {
                echo '<li class="sub-menu">';
            } else {
                echo '<li class="' . $class . '">';
            }
            $url = $url == '#' ? 'javascript:;' : Url::toRoute($url);

            echo '<a href="' . $url . '"  class="' . $class . '">'
                . ($menuArray['data']['icon'] ? '<i class="fa ' . $menuArray['data']['icon'] . '"></i>' : '') . '<span>'
                . $menuArray['label'] . '</span></a>';

            if (isset($menuArray['items'])) {
                echo '<ul class="sub">';
                foreach ($menuArray['items'] as $item) {

                    echo initMenu($item, $controllerName, $isSubUrl);
                }
                echo '</ul>';
            }

            echo '</li>';
        }

    }

}

?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <ul class="sidebar-menu" id="nav-accordion">
            <li>
                <a class="<?= ($controllerName == 'site' ? 'active' : '') ?>" href="<?= Url::home() ?>">
                    <i class="fa fa-dashboard"></i>
                    <span><?= Yii::t('admin', '首页') ?></span>
                </a>
            </li>
            <?php

            if (isset($menuRows)) {

                $isSubUrl = false;
                foreach ($menuRows as $menuRow) {

                    $isSubUrl = isSubUrl($menuRow, $route);

                    if ($isSubUrl) {
                        break;
                    }


                }
                foreach ($menuRows as $menuRow) {

                    initMenu($menuRow, $controllerName, $isSubUrl, true);
                }
            }
            ?>

        </ul>
<!--        <ul class="sidebar-menu">-->
<!--            <li class="treeview">-->
<!--                <a href="#">-->
<!--                    <i class="fa fa-gears"></i> <span>权限控制</span>-->
<!--                    <i class="fa fa-angle-left pull-right"></i>-->
<!--                </a>-->
<!---->
<!--                <ul class="treeview-menu">-->
<!--                    <li><a href="/user"><i class="fa fa-user-circle-o"></i> 后台用户</a></li>-->
<!--                    <li class="treeview">-->
<!--                        <a href="/admin/role">-->
<!--                            <i class="fa fa-circle-o"></i> 权限 <i class="fa fa-angle-left pull-right"></i>-->
<!--                        </a>-->
<!--                        <ul class="treeview-menu">-->
<!--                            <li><a href="/admin/route"><i class="fa fa-circle-o"></i> 路由</a></li>-->
<!--                            <li><a href="/admin/permission"><i class="fa fa-circle-o"></i> 权限</a></li>-->
<!--                            <li><a href="/admin/role"><i class="fa fa-circle-o"></i> 角色</a></li>-->
<!--                            <li><a href="/admin/assignment"><i class="fa fa-circle-o"></i> 分配</a></li>-->
<!--                            <li><a href="/admin/menu"><i class="fa fa-circle-o"></i> 菜单</a></li>-->
<!--                        </ul>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </li>-->
<!--        </ul>-->

<!--                --><?//= dmstr\widgets\Menu::widget(
//                    [
//                        'options' => ['class' => 'sidebar-menu'],
//                        'items' => [
//                            ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
//                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
//                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
//                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
//                            [
//                                'label' => 'Same tools',
//                                'icon' => 'share',
//                                'url' => '#',
//                                'items' => [
//                                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
//                                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
//                                    [
//                                        'label' => 'Level One',
//                                        'icon' => 'circle-o',
//                                        'url' => '#',
//                                        'items' => [
//                                            ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
//                                            [
//                                                'label' => 'Level Two',
//                                                'icon' => 'circle-o',
//                                                'url' => '#',
//                                                'items' => [
//                                                    ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
//                                                    ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
//                                                ],
//                                            ],
//                                        ],
//                                    ],
//                                ],
//                            ],
//                        ],
//                    ]
//                ) ?>

    </section>

</aside>
