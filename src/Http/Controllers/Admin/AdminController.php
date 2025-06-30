<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Http\Controllers\Admin;

use VendorName\Skeleton\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Class AdminController
 * Controller básico para operações CRUD
 */
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render($this->getViewIndex(), $this->getDataForViews($request));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render($this->getViewCreate(), $this->getDataForViewsCreate($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->createRecord($request->all());
        
        return redirect()->route($this->getRouteName('index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        return Inertia::render($this->getViewShow(), $this->getDataForViewsShow($request, $id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        return Inertia::render($this->getViewEdit(), $this->getDataForViewsEdit($request, $id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->updateRecord($id, $request->all());
        
        return redirect()->route($this->getRouteName('index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $this->deleteRecord($id);
        
        return redirect()->route($this->getRouteName('index'));
    }
}
