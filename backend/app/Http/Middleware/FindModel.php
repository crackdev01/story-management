<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Check model if it exists
 */
class FindModel
{
    public function handle($request, Closure $next, string $modelClass)
    {
        $id = $request->route('id');
        $model = $modelClass::find($id);

        if (!$model) {
            return errorResponse(101, 'Data not found', 404);    
        }

        // Store the found model in the request attributes
        $request->attributes->set('model', $model);

        return $next($request);
    }
}
