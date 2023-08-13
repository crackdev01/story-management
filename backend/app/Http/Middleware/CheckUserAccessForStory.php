<?php

namespace App\Http\Middleware;

use App\Models\Story;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check user access for story
 */

class CheckUserAccessForStory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $storyId = $request->route('id');
        $story = Story::findOrFail($storyId);

        // If action is show and story is published, allow access
        if($request->route()->getActionMethod() == "show" && $story->status == Story::STATUSES[1]){
            return $next($request);
        }

        // If user does not have scope of 'users-manage-all' and is not the owner of the story, return error
        if (
            !$request->user()->tokenCan('users-manage-all') &&
            !in_array($request->user()->id,  $story->users()->pluck('id')->toArray())
        ) {
            
            return errorResponse(62, 'Sorry, you are not authorized to access this story', 403);
        }

        return $next($request);
    }
}
