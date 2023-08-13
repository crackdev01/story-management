<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Default sort by id desc
        $sortArray = $request->sort ? explode("-", $request->sort) : ['id', 'desc'];
        
        // If has scope of 'users-manage-all', get all stories
        if ($request->user()->tokenCan('users-manage-all')) {     
            // Get all stories 
            $stories = StoryResource::collection(
                Story::
                    when($request->has('count') && $request->has('offset'), function ($query) use ($request) {
                        return $query->skip($request->offset ?: 0)->take($request->count);
                    })
                    ->orderBy($sortArray[0], $sortArray[1])->get()
            );   
            $total = Story::all()->count();
        }else{
            // Get user's own stories and stories with status 'Published'
            $query = Story::
                whereHas('users', function ($query) use ($request) {
                    $query->where('users.id', $request->user()->id);
                })
                ->orWhere('status', Story::STATUSES[1]);
            $stories = StoryResource::collection(
                $query
                    ->when($request->has('count') && $request->has('offset'), function ($query) use ($request) {
                        return $query->skip($request->offset ?: 0)->take($request->count);
                    })
                    ->orderBy($sortArray[0], $sortArray[1])->get()
            );
            $total = $query->count();           
        }

        return response()->json([                               
            'data'=> $stories,
            'total' => $total
        ]);  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {            
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:stories|max:255',
            'content' => 'required|string'  
        ]);
        if ($validator->fails()) {
            return errorResponse(100, $validator->errors()->first(), 400);          
        }    
      
        $data = $request->only(['title', 'content']);

        // Assign current user as the owner of the story
        $story = $request->user()->stories()->create($data);

        return new StoryResource($story);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {        
        // Model is passed from middleware find_model  
        $story = $request->get('model');

        return new StoryResource($story);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Model is passed from middleware find_model  
        $story = $request->get('model');

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255|unique:stories,title,'.$story->id,         
            'content' => 'string',
            'status' => 'in:' . implode(',', Story::STATUSES),
            'user_ids' => 'nullable|array', // Nullable array of user IDs for assignment of story's owners
        ]);
        if ($validator->fails()) {
            return errorResponse(100, $validator->errors()->first(), 400, $validator->errors());               
        }            

        $data = $request->only(['title', 'content', 'status']);   
        $story->update($data);

         // If user_ids are provided, sync new owners to the story
         if ($request->has('user_ids') && is_array($request->input('user_ids'))) {
            $users = User::whereIn('id', $request->input('user_ids'))->get();
            $story->users()->sync($users);
        }

        return new StoryResource($story);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Model is passed from middleware find_model  
        $story = $request->get('model');

        $story->delete();
        
        return response()->json([                                         
            'message' => 'Story is deleted successfully'
        ]);  
    }
}
