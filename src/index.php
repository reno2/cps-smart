<?

use Cps\Smart\Request;
use Cps\Smart\Response;
use Cps\Smart\SmartAI;

require_once __DIR__ . "/vendor/autoload.php";



$request = new Request();
$request->setRouteParams($request->getBody());


if ($request->isPost()) {

    if ($payload = $request->getRouteParam('payload')) {
        $response = new Response();
        $response->sendJson([
            'status' => 'success',
            'data' => (new SmartAI($payload))->getSmartTitle()
        ]);
    }
}



if ($request->isGet()) {
    if ($payload = $request->getRouteParam('payload')) {
        echo $payload;
    }
}





