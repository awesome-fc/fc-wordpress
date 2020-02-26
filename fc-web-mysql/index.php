<?php
use RingCentral\Psr7\Response;

function handler($request, $context): Response
{
    $host = getenv("WEB_HOST");
    $root_dir = '/mnt/auto/wordpress'; // nas dir
    $uri = $request->getAttribute("requestURI");
    $uriArr = explode("?", $uri);
    // default php / or /wp-admin/
    if (preg_match('#/$#', $uriArr[0]) && !(strpos($uri, '.php'))) {
        $uriArr[0] .= "index.php";
        $uri = implode($uriArr);
    }
    //php script
    if (preg_match('#\.php.*#', $uri)) {
        try{
            $resp = $GLOBALS['fcPhpCgiProxy']->requestPhpCgi(
                    $request, $root_dir, "index.php",
                    ['HTTP_HOST' => $host, 'SERVER_NAME' => $host, 'SERVER_PORT' => '80'],
                    ['debug_show_cgi_params' => true, 'readWriteTimeout' => 15000]
                );
            return $resp;

        } catch (Exception $e){
            $GLOBALS['fcPhpCgiProxy'] = new \ServerlessFC\PhpCgiProxy();
            $resp = $GLOBALS['fcPhpCgiProxy']->requestPhpCgi(
                    $request, $root_dir, "index.php",
                    ['HTTP_HOST' => $host, 'SERVER_NAME' => $host, 'SERVER_PORT' => '80'],
                    ['debug_show_cgi_params' => true, 'readWriteTimeout' => 15000]
                );
            return $resp;
        } 
    } else {
        // static files, js, css, jpg ...
        $filename = $root_dir . explode("?", $uri)[0];
        $filename = rawurldecode($filename);
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        $headers = [
            'Content-Type' => $GLOBALS['fcPhpCgiProxy']->getMimeType($filename),
            'Cache-Control' => "max-age=8640000",
            'Accept-Ranges' => 'bytes',
        ];
        return new Response(200, $headers, $contents);
    }
}
