<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JwtAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        // ❌ No token
        if (!$header) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Token required'
            ]);
        }

        // Extract token
        $parts = explode(' ', $header);

        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid token format'
            ]);
        }

        $token = $parts[1];

        // Validate token
        $decoded = validateJWT($token);

        if (!$decoded) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid or expired token'
            ]);
        }

        // ✅ Attach user data to request (VERY USEFUL)
        service('request')->user = $decoded->data;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
