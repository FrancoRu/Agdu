<?php
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Frame-Options: DENY');
header('Strict-Transport-Security: max-age=31536000');
header("Content-Security-Policy: default-src 'self'");
header('Referrer-Policy: no-referrer');
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/BeneficiaryController.php';
require_once __DIR__ . '/controllers/AdministratorController.php';
require_once __DIR__ . '/libs/chargeFile.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

session_start();

$action = ($_SERVER['REQUEST_METHOD'] === 'GET') ? ($_GET['action'] ?? '') : ($_POST['action'] ?? '');

switch ($action) {
    case 'panel':
        AdministratorController::PanelOrLogin();
        break;
    case 'login':
        AdministratorController::login();
        break;
    case 'logout':
        AdministratorController::logOut();
        break;
    case 'download':
        AdministratorController::downloadFile();
        break;
    case 'downloadXLSX':
        AdministratorController::downloadXLSX();
        break;
    case 'home':
        BeneficiaryController::chargeUser();
        break;
    case 'upload':
        BeneficiaryController::setFile();
        break;
    case 'children':
        loadView('views/layout/formChildren.php', 200);
        break;
    case '':
        loadView('views/Beneficiary/BeneficiaryIngress.php', 403);
        break;
    default:
        loadView('views/Beneficiary/BeneficiaryIngress.php', 200);
        break;
}
