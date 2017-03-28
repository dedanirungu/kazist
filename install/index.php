<?php
define('KAZIST', true);
define('SYSTEM_INSTALL', true);
define('JPATH_TEMPLATES', __DIR__ . '/themes');

require_once __DIR__ . '/Setup.php';
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$sc = include __DIR__ . '/../include/container.php';

$setup = new Setup($sc);
$step = $setup->getStep();
$action = $setup->getAction();
$message = $setup->getMessage();
$unique = uniqid();

$setup->clearMessage();

if ($action == 'save') {
    $setup->getSave();
} elseif ($action == 'savetofile') {
    $setup->getSaveToFile();
} else {
    switch ($step) {
        case 2:
            $setup->stepOneVerification();
            $html = $setup->stepTwo();
            break;
        case 3:
            $setup->stepOneVerification();
            $setup->stepTwoVerification();
            $html = $setup->stepThree();
            break;
        case 4:
            $setup->stepOneVerification();
            $setup->stepTwoVerification();
            $setup->stepThreeVerification();
            $html = $setup->stepFour();
            break;
        case 5:
            $html = $setup->stepFive();
            break;
        case 1:
        default:
            $html = $setup->stepOne();
            break;
    }
}
?>

<html>
    <head>

        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />

        <link rel='stylesheet'  href='../assets/css/bootstrap.css' type='text/css' media='all' />
        <link rel='stylesheet'  href='../assets/css/font-awesome.css' type='text/css' media='all' />
    </head>
    <div class="installer-wrapper">

        <div class="text-center">
            <img src="../assets/images/logo.png" width="64px">
        </div>
        <hr>

        <?php if ($message <> ''): ?>
            <div class="alert alert-danger">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <ul id="kazi-system" class="nav nav-pills">
            <li <?php if ($step == 1): ?>class="active"<?php endif; ?>>
                <a href="index.php?step=1&token=<?php echo $unique; ?>" data-toggle="tab" title=" Database Setup">
                    <i class="fa fa-database"></i> 
                    Database Setup
                </a>
            </li>
            <li <?php if ($step == 2): ?>class="active"<?php endif; ?>>
                <a href="index.php?step=2&token=<?php echo $unique; ?>" data-toggle="tab" title="Admin Profile">
                    <i class="fa fa-user"></i> 
                    Admin Profile
                </a>
            </li>
            <li <?php if ($step == 3): ?>class="active"<?php endif; ?>>
                <a href="index.php?step=3&token=<?php echo $unique; ?>" data-toggle="tab" title="System Config">
                    <i class="fa fa-gears"></i> 
                    System Config
                </a>
            </li>
            <li <?php if ($step == 4): ?>class="active"<?php endif; ?>>
                <a href="index.php?step=4&token=<?php echo $unique; ?>" data-toggle="tab" title="Verify Information">
                    <i class="fa fa-compress"></i> 
                    Verify Information
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
        <br>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active">
                <?php echo $html ?>
            </div>


        </div>


    </div>

    <style>
        html,body{
            background: #f6f5f2;
        }
        .installer-wrapper{
            border:2px solid #eee;
            max-width: 700px;
            margin: 10px auto;
            background: white;
            padding:10px;
        }

        .valign-middle{
            vertical-align: middle;
        }
    </style>
</html>

