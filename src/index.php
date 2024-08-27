<?


use Cps\Smart\Request;

require_once __DIR__ . "/vendor/autoload.php";

$request = new Request();
$request->setRouteParams($request->getBody());


if ($request->isPost()) {
    if ($payload = $request->getRouteParam('payload')) {
        echo $payload;
    }
}





