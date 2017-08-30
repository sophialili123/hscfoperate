<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;

//use yii\helpers\Html;
//use yii\bootstrap\Nav;
//use yii\bootstrap\NavBar;
//use yii\widgets\Breadcrumbs;
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
            $class = ' active dcjq-parent';
        } else {
            $class = '';
        }


        if (empty($menuArray['data']['hide'])) {
            if (isset($menuArray['items'])) {
                echo '<li class="sub-menu dcjq-parent-li">';
            } else {
                echo '<li class="' . $class . '">';
            }
            $url = $url == '#' ? 'javascript:;' : Url::toRoute($url);

            echo '<a href="' . $url . '"  class="' . $class . '">'
                . ($menuArray['data']['icon'] ? '<i class="fa ' . $menuArray['data']['icon'] . '"></i>' : '') . '<span>'
                . $menuArray['label'] . '</span><span class="dcjq-icon"></span></a>';

            if (isset($menuArray['items'])) {
                echo '<ul class="nav nav-second-level collapse in">';
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
<!--        <div class="user-panel">-->
<!--            <div class="pull-left image">-->
<!--                <img src="--><?//= $directoryAsset ?><!--/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>-->
<!--            </div>-->
<!--            <div class="pull-left info">-->
<!--                <p>Alexander Pierce</p>-->
<!---->
<!--                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
<!--            </div>-->
<!--        </div>-->

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">-->
<!--            <div class="input-group">-->
<!--                <input type="text" name="q" class="form-control" placeholder="Search..."/>-->
<!--              <span class="input-group-btn">-->
<!--                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--            </div>-->
<!--        </form>-->
        <!-- /.search form -->
        <div id="sidebar" class="nav-collapse ">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="<?= ($controllerName == 'site' ? 'active' : '') ?>" href="<?= Url::home() ?>">
                        <i class="fa fa-dashboard"></i>
                        <span><?= Yii::t('admin', '管理首页') ?></span>
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
            <!-- sidebar menu end-->
        </div>
    </section>

</aside>
