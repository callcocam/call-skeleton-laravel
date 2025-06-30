<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Http\Controllers\Landlord;

use VendorName\Skeleton\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Class DashboardController
 * Controller básico para dashboard do landlord
 */
class DashboardController extends Controller
{
    /**
     * Display the landlord dashboard.
     */
    public function index()
    {
        return Inertia::render('landlord/dashboard', $this->getViewData());
    }

    /**
     * Get view data for the landlord dashboard.
     */
    protected function getViewData()
    {
        $landlord = Auth::guard('landlord')->user();
        
        return [
            'title' => 'Landlord Dashboard',
            'description' => 'Welcome to the Landlord Dashboard',
            'auth' => [
                'landlord' => $landlord
            ],
        ];
    }
}
