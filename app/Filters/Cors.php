<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 🔥 GLOBAL ALLOW (sabko allow karega)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With");

        // Optional but useful
        header("Access-Control-Max-Age: 86400");

        // 🔥 MOST IMPORTANT (preflight fix)
        if ($request->getMethod() === 'options') {
            return service('response')
                ->setStatusCode(200)
                ->setBody('OK');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Response me bhi headers ensure kar do
        return $response
            ->setHeader("Access-Control-Allow-Origin", "*")
            ->setHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS")
            ->setHeader("Access-Control-Allow-Headers", "Origin, Content-Type, Accept, Authorization, X-Requested-With");
    }
}
